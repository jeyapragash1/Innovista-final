<?php
require_once '../config/session.php'; 
protectPage('customer');

$pageTitle = 'My Projects';
require_once '../includes/user_dashboard_header.php'; 
require_once '../config/Database.php';  

$customer_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();

// Fetching all project types for this page
$active_projects_query = $db->prepare("SELECT prov.name as provider_name, q.project_description, p.status, p.quotation_id FROM projects p JOIN quotations q ON p.quotation_id = q.id JOIN users prov ON q.provider_id = prov.id WHERE q.customer_id = :id AND p.status IN ('in_progress', 'awaiting_final_payment') ORDER BY p.id DESC");
$active_projects_query->bindParam(':id', $customer_id);
$active_projects_query->execute();
$active_projects = $active_projects_query->fetchAll(PDO::FETCH_ASSOC);

$pending_quotes_query = $db->prepare("SELECT prov.name as provider_name, q.project_description, q.status, q.id as quotation_id FROM quotations q JOIN users prov ON q.provider_id = prov.id WHERE q.customer_id = :id AND q.status IN ('quote_sent', 'Awaiting Quote') ORDER BY q.id DESC");
$pending_quotes_query->bindParam(':id', $customer_id);
$pending_quotes_query->execute();
$pending_quotes = $pending_quotes_query->fetchAll(PDO::FETCH_ASSOC);

$completed_projects_query = $db->prepare("SELECT prov.name as provider_name, q.project_description, p.quotation_id FROM projects p JOIN quotations q ON p.quotation_id = q.id JOIN users prov ON q.provider_id = prov.id WHERE q.customer_id = :id AND p.status = 'completed' ORDER BY p.id DESC");
$completed_projects_query->bindParam(':id', $customer_id);
$completed_projects_query->execute();
$completed_projects = $completed_projects_query->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Projects & Quotations</h2>
<p>Track your active projects, respond to quotes, and view your completed work.</p>

<div class="dashboard-section">
    <h3>Active Projects</h3>
    <div class="content-card">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Provider</th><th>Project</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (!empty($active_projects)): foreach ($active_projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['project_description']); ?></td>
                        <td><span class="status-badge status-pending"><?php echo ucwords(str_replace('_', ' ', $project['status'])); ?></span></td>
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
    <h3>Pending Quotations</h3>
    <div class="content-card">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Provider</th><th>Project</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (!empty($pending_quotes)): foreach ($pending_quotes as $quote): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quote['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($quote['project_description']); ?></td>
                        <td><span class="status-badge status-pending"><?php echo ($quote['status'] === 'quote_sent') ? 'Quote Sent & Wait for Booking' : ucwords(str_replace('_', ' ', $quote['status'])); ?></span></td>
                        <td>
                            <a href="view_quote.php?id=<?php echo $quote['quotation_id']; ?>" class="btn-view">View & Respond</a>
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

<div class="dashboard-section">
    <h3>Completed Projects</h3>
    <div class="content-card">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Provider</th><th>Project</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php if (!empty($completed_projects)): foreach ($completed_projects as $project): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($project['provider_name']); ?></td>
                        <td><?php echo htmlspecialchars($project['project_description']); ?></td>
                        <td><span class="status-badge status-approved">Completed</span></td>
                        <td><a href="view_project_history.php?id=<?php echo $project['quotation_id']; ?>" class="btn-view">View & Review</a></td>
                    </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" style="text-align: center;">You have no completed projects.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>