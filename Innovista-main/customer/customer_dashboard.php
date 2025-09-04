<?php
// Include session and protect the page FIRST
require_once '../config/session.php'; 
protectPage('customer'); 

// Now, include all other necessary files
$pageTitle = 'Customer Dashboard';
require_once '../includes/user_dashboard_header.php'; 
require_once '../config/Database.php';

// Get the customer ID from the session
$customer_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();

// --- Fetch Stats for Cards ---
$stmt_active = $db->prepare("SELECT COUNT(p.id) as count FROM projects p JOIN quotations q ON p.quotation_id = q.id WHERE q.customer_id = :id AND p.status IN ('in_progress', 'awaiting_final_payment')");
$stmt_active->bindParam(':id', $customer_id);
$stmt_active->execute();
$active_projects_count = $stmt_active->fetch(PDO::FETCH_ASSOC)['count'];

// Count pending quotes from both tables
$stmt_pending = $db->prepare("SELECT COUNT(*) as count FROM quotations WHERE customer_id = :id AND status IN ('Awaiting Quote', 'Awaiting Your Quote', 'quote_sent')");
$stmt_pending->bindParam(':id', $customer_id);
$stmt_pending->execute();
$pending_quotes_count = $stmt_pending->fetch(PDO::FETCH_ASSOC)['count'];

$stmt_custom_pending = $db->prepare("SELECT COUNT(*) as count FROM custom_quotations WHERE customer_id = :id AND status = 'sent'");
$stmt_custom_pending->bindParam(':id', $customer_id);
$stmt_custom_pending->execute();
$pending_quotes_count += $stmt_custom_pending->fetch(PDO::FETCH_ASSOC)['count'];
$unread_messages_count = 3; // Placeholder

// --- Fetch Data for Tables ---
$active_projects_query = $db->prepare("SELECT prov.name as provider_name, q.project_description, p.status, p.quotation_id FROM projects p JOIN quotations q ON p.quotation_id = q.id JOIN users prov ON q.provider_id = prov.id WHERE q.customer_id = :id AND p.status IN ('in_progress', 'awaiting_final_payment') ORDER BY p.id DESC");
$active_projects_query->bindParam(':id', $customer_id);
$active_projects_query->execute();
$active_projects = $active_projects_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending quotations from both quotations and custom_quotations
$pending_quotes_query = $db->prepare("
    SELECT prov.name as provider_name, q.project_description, q.status, q.id as quotation_id
    FROM quotations q
    JOIN users prov ON q.provider_id = prov.id
    WHERE q.customer_id = :id AND q.status IN ('Awaiting Quote', 'Awaiting Your Quote', 'quote_sent')
    UNION ALL
    SELECT prov.name as provider_name, cq.project_description, cq.status, cq.id as quotation_id
    FROM custom_quotations cq
    JOIN users prov ON cq.provider_id = prov.id
    WHERE cq.customer_id = :id AND cq.status = 'sent'
    ORDER BY quotation_id DESC
");
$pending_quotes_query->bindParam(':id', $customer_id);
$pending_quotes_query->execute();
$pending_quotes = $pending_quotes_query->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Customer Dashboard</h2>
<p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! Manage your projects and find new services here.</p>

<div class="stats-container-customer">
    <div class="stat-card-customer">
        <div class="stat-icon-customer blue"><i class="fas fa-tasks"></i></div>
        <div class="stat-info-customer"><h4>Active Projects</h4><p><?php echo $active_projects_count; ?></p></div>
    </div>
    <div class="stat-card-customer">
        <div class="stat-icon-customer yellow"><i class="fas fa-file-invoice"></i></div>
        <div class="stat-info-customer"><h4>Pending Quotes</h4><p><?php echo $pending_quotes_count; ?></p></div>
    </div>
    <div class="stat-card-customer">
        <div class="stat-icon-customer purple"><i class="fas fa-comments"></i></div>
        <div class="stat-info-customer"><h4>Unread Messages</h4><p><?php echo $unread_messages_count; ?></p></div>
    </div>
</div>

<div class="dashboard-section">
    <h3>What would you like to do?</h3>
    <div class="quick-access-grid">
        <a href="request_quotation.php" class="access-card"><i class="fas fa-file-signature"></i><span>Request a New Quote</span></a>
        <a href="../public/services.php" class="access-card"><i class="fas fa-search"></i><span>Search for Services</span></a>
        <a href="../public/products.php" class="access-card"><i class="fas fa-shopping-cart"></i><span>Purchase Products</span></a>
        <a href="my_profile.php" class="access-card"><i class="fas fa-user-edit"></i><span>Manage My Profile</span></a>
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
                            <td><span class="status-badge status-pending"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $project['status']))); ?></span></td>
                            <td><a href="track_project.php?id=<?php echo $project['quotation_id']; ?>" class="btn-view">Track Project</a></td>
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
                    <?php if (!empty($pending_quotes)): foreach ($pending_quotes as $quote): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quote['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($quote['project_description']); ?></td>
                        <td><span class="status-badge status-pending"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $quote['status']))); ?></span></td>
                        <td><a href="view_quote.php?id=<?php echo $quote['quotation_id']; ?>" class="btn-view">View & Respond</a></td>
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