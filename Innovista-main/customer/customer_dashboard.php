<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\customer_dashboard.php

// Include session and protect the page FIRST
require_once '../public/session.php'; // Defines isUserLoggedIn, getUserRole, getUserId, getImageSrc
require_once '../handlers/flash_message.php'; // For display_flash_message

// --- User-specific authentication function ---
// This function should be defined in public/session.php or a dedicated auth.php
if (!function_exists('protectPage')) {
    function protectPage(string $requiredRole): void {
        if (!isUserLoggedIn()) {
            header("Location: ../public/login.php");
            exit();
        }
        // Allow admin to view customer/provider dashboards for management purposes
        if (getUserRole() !== $requiredRole && getUserRole() !== 'admin') { 
            set_flash_message('error', 'Access denied. You do not have permission to view this page.');
            header("Location: ../public/index.php"); // Redirect to homepage or appropriate dashboard
            exit();
        }
    }
}
protectPage('customer'); 

// Now, include all other necessary files
$pageTitle = 'Customer Dashboard';
require_once '../includes/user_dashboard_header.php'; // This header will now have access to getImageSrc
require_once '../config/Database.php';

// Get the customer ID and Name from the session
$customer_id = getUserId();
$customer_name = htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); // Fallback name

$database = new Database();
$conn = $database->getConnection(); // FIX: Get the PDO connection object

// --- Fetch Stats for Cards ---

// 1. Active Projects Count
// Updated to explicitly join custom_quotations as projects reference custom_quotations.id
$stmt_active = $conn->prepare("SELECT COUNT(p.id) as count FROM projects p JOIN custom_quotations cq ON p.quotation_id = cq.id WHERE cq.customer_id = :id AND p.status IN ('in_progress', 'awaiting_final_payment', 'disputed')");
$stmt_active->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_active->execute();
$active_projects_count = $stmt_active->fetch(PDO::FETCH_ASSOC)['count'];

// 2. Pending Quotes Count
// Original requests not yet quoted by provider (q.status = 'Awaiting Quote')
// Custom quotes sent by provider, awaiting customer action (cq.status = 'sent' or 'pending')
$stmt_original_pending = $conn->prepare("
    SELECT COUNT(q.id) AS count
    FROM quotations q
    WHERE q.customer_id = :id AND q.status = 'Awaiting Quote'
");
$stmt_original_pending->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_original_pending->execute();
$original_quotes_pending_count = $stmt_original_pending->fetch(PDO::FETCH_ASSOC)['count'];

$stmt_custom_awaiting_customer = $conn->prepare("
    SELECT COUNT(cq.id) AS count
    FROM custom_quotations cq
    WHERE cq.customer_id = :id AND cq.status IN ('sent', 'pending')
");
$stmt_custom_awaiting_customer->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_custom_awaiting_customer->execute();
$custom_quotes_awaiting_customer_count = $stmt_custom_awaiting_customer->fetch(PDO::FETCH_ASSOC)['count'];

$pending_quotes_total_count = $original_quotes_pending_count + $custom_quotes_awaiting_customer_count;


// 3. Unread Messages Count
// Assuming messages in 'contacts' table are linked via email and 'is_read' flag
// And assuming a customer can have messages directly (not just disputes/quotes)
$unread_messages_count = 0;
// First get the customer's email
$stmt_customer_email = $conn->prepare("SELECT email FROM users WHERE id = :id");
$stmt_customer_email->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_customer_email->execute();
$customer_email_result = $stmt_customer_email->fetch(PDO::FETCH_ASSOC);

if ($customer_email_result) {
    $customer_actual_email = $customer_email_result['email'];
    $stmt_unread_messages = $conn->prepare("SELECT COUNT(*) as count FROM contacts WHERE email = :email AND is_read = 0");
    $stmt_unread_messages->bindParam(':email', $customer_actual_email);
    $stmt_unread_messages->execute();
    $unread_messages_count = $stmt_unread_messages->fetch(PDO::FETCH_ASSOC)['count'];
}


// NEW STAT 1: Completed Projects Count
$completed_projects_count = 0;
$stmt_completed = $conn->prepare("SELECT COUNT(p.id) as count FROM projects p JOIN custom_quotations cq ON p.quotation_id = cq.id WHERE cq.customer_id = :id AND p.status = 'completed'");
$stmt_completed->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_completed->execute();
$completed_projects_count = $stmt_completed->fetch(PDO::FETCH_ASSOC)['count'];


// NEW STAT 2: Total Payments Made (Sum of amounts where payment_type is 'final' or 'advance')
$total_payments_made = 0;
$stmt_payments_total = $conn->prepare("SELECT SUM(py.amount) as total_paid FROM payments py JOIN custom_quotations cq ON py.quotation_id = cq.id WHERE cq.customer_id = :id");
$stmt_payments_total->bindParam(':id', $customer_id, PDO::PARAM_INT);
$stmt_payments_total->execute();
$total_payments_made = $stmt_payments_total->fetch(PDO::FETCH_ASSOC)['total_paid'] ?? 0;


// --- Fetch Data for Tables ---
// Active Projects: link projects to custom_quotations for description
$active_projects_query = $conn->prepare("
    SELECT prov.name as provider_name, cq.project_description, p.status, p.id as project_id, q.id as quotation_request_id, cq.id as custom_quotation_id
    FROM projects p
    JOIN custom_quotations cq ON p.quotation_id = cq.id
    JOIN quotations q ON cq.quotation_id = q.id
    JOIN users prov ON cq.provider_id = prov.id
    WHERE cq.customer_id = :id AND p.status IN ('in_progress', 'awaiting_final_payment', 'disputed')
    ORDER BY p.id DESC
");
$active_projects_query->bindParam(':id', $customer_id, PDO::PARAM_INT);
$active_projects_query->execute();
$active_projects = $active_projects_query->fetchAll(PDO::FETCH_ASSOC);


// Fetch pending quotations from both quotations (original requests) and custom_quotations (provider-sent quotes)
$pending_quotes_query = $conn->prepare("
    -- Original requests awaiting a custom quote from provider
    SELECT 'original' as quote_type, q.id as entity_id, prov.name as provider_name, q.project_description, q.status, q.created_at
    FROM quotations q
    JOIN users prov ON q.provider_id = prov.id
    WHERE q.customer_id = :id AND q.status = 'Awaiting Quote'
    UNION ALL
    -- Custom quotes sent by provider, awaiting customer's decision
    SELECT 'custom' as quote_type, cq.id as entity_id, prov.name as provider_name, cq.project_description, cq.status, cq.created_at
    FROM custom_quotations cq
    JOIN users prov ON cq.provider_id = prov.id
    WHERE cq.customer_id = :id AND cq.status IN ('sent', 'pending') -- 'pending' might mean awaiting customer approval
    ORDER BY created_at DESC
");
$pending_quotes_query->bindParam(':id', $customer_id, PDO::PARAM_INT);
$pending_quotes_query->execute();
$pending_quotes = $pending_quotes_query->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Header HTML provided by user_dashboard_header.php -->

<h2>Customer Dashboard</h2>
<p>Welcome back, <?php echo $customer_name; ?>! Manage your projects and find new services here.</p>

<div class="stats-container-customer">
    <div class="stat-card-customer">
        <div class="stat-icon-customer blue"><i class="fas fa-tasks"></i></div>
        <div class="stat-info-customer"><h4>Active Projects</h4><p><?php echo $active_projects_count; ?></p></div>
    </div>
    <div class="stat-card-customer">
        <div class="stat-icon-customer yellow"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info-customer"><h4>Pending Quotes</h4><p><?php echo $pending_quotes_total_count; ?></p></div>
    </div>
    <div class="stat-card-customer">
        <div class="stat-icon-customer purple"><i class="fas fa-comments"></i></div>
        <div class="stat-info-customer"><h4>Unread Messages</h4><p><?php echo $unread_messages_count; ?></p></div>
    </div>
    <!-- NEW STAT CARD 1: Completed Projects -->
    <div class="stat-card-customer">
        <div class="stat-icon-customer green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info-customer"><h4>Completed Projects</h4><p><?php echo $completed_projects_count; ?></p></div>
    </div>
    <!-- NEW STAT CARD 2: Total Payments Made -->
    <div class="stat-card-customer">
        <div class="stat-icon-customer primary-color-bg"><i class="fas fa-wallet"></i></div>
        <div class="stat-info-customer"><h4>Total Paid</h4><p>Rs <?php echo number_format($total_payments_made, 2); ?></p></div>
    </div>
</div>

<div class="dashboard-section">
    <h3>What would you like to do?</h3>
    <div class="quick-access-grid">
    <a href="request_quotation.php" class="access-card"><i class="fas fa-file-signature"></i><span>Request a New Quote</span></a>
    <a href="../public/services.php" class="access-card"><i class="fas fa-search"></i><span>Search for Services</span></a>
    <a href="../public/product.php" class="access-card"><i class="fas fa-shopping-cart"></i><span>Purchase Products</span></a>
    <a href="my_profile.php" class="access-card"><i class="fas fa-user-edit"></i><span>Manage My Profile</span></a>
    <a href="my_projects.php" class="access-card"><i class="fas fa-project-diagram"></i><span>My Projects & Quotes</span></a>
    <a href="payment_history.php" class="access-card"><i class="fas fa-history"></i><span>Payment History</span></a>
    </div>
</div>

    <div class="dashboard-section">
        <h3>My Active Projects</h3>
        <div class="content-card">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Provider</th><th>Project</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (!empty($active_projects)): foreach ($active_projects as $project): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($project['provider_name']); ?></td>
                            <td><?php echo htmlspecialchars($project['project_description']); ?></td>
                            <td><span class="status-badge status-<?php 
                                // Dynamic status badge class
                                $status_lower = strtolower($project['status']);
                                if ($status_lower === 'in_progress') echo 'pending'; // In-progress is kind of pending
                                elseif ($status_lower === 'awaiting_final_payment') echo 'yellow'; 
                                elseif ($status_lower === 'completed') echo 'approved'; // Should not be in active projects if completed
                                elseif ($status_lower === 'disputed') echo 'rejected';
                                else echo 'info'; // Fallback
                            ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $project['status']))); ?></span></td>
                            <td><a href="track_project.php?id=<?php echo htmlspecialchars($project['custom_quotation_id']); ?>" class="btn-view">Track Project</a></td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" style="text-align: center;">You have no active projects.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<div class="dashboard-section">
    <h3>My Pending Quotations</h3>
    <div class="content-card">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Provider</th><th>Project</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (!empty($pending_quotes)): foreach ($pending_quotes as $quote): 
                        // The entity_id refers to quotations.id for 'original' and custom_quotations.id for 'custom'
                        $view_quote_id = htmlspecialchars($quote['entity_id']);
                        $quote_type_param = htmlspecialchars($quote['quote_type']); // Pass this to differentiate in view_quote.php

                        $status_badge_class = 'info'; // Default
                        $status_lower = strtolower($quote['status']);
                        if ($status_lower === 'awaiting quote') {
                            $status_badge_class = 'pending';
                        } elseif ($status_lower === 'sent') { // Provider sent quote, customer needs to respond
                            $status_badge_class = 'yellow';
                        } elseif ($status_lower === 'approved') {
                             $status_badge_class = 'approved';
                        } elseif ($status_lower === 'rejected' || $status_lower === 'declined') {
                            $status_badge_class = 'rejected';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quote['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($quote['project_description']); ?></td>
                        <td><span class="status-badge status-<?php echo $status_badge_class; ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $quote['status']))); ?></span></td>
                        <td>
                            <a href="view_quote.php?id=<?php echo $view_quote_id; ?>&type=<?php echo $quote_type_param; ?>" class="btn-view">View & Respond</a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" style="text-align: center;">You have no pending quotations.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>