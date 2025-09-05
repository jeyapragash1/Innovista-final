<?php 
require_once 'admin_header.php'; 
require_once '../config/Database.php';

// Initialize DB
$db = new Database();
$conn = $db->getConnection();

// --- FETCH DATA FOR DASHBOARD ---

// 1. Total Revenue (sum of payments)
$total_revenue = 0;
$stmt = $conn->query("SELECT SUM(amount) as revenue FROM payments");
if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_revenue = $row['revenue'] ?? 0;
}

// 2. Active Users (customers + providers with status='active')
$active_users = 0;
$stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE status='active'");
if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $active_users = $row['total'];
}

// 3. Pending Providers
$pending_providers = 0;
$stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='provider' AND provider_status='pending'");
if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $pending_providers = $row['total'];
}

// 4. New Messages (contacts table count)
$new_messages = 0;
$stmt = $conn->query("SELECT COUNT(*) as total FROM contacts");
if ($stmt) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $new_messages = $row['total'];
}

// 5. Recent Registrations (latest 5 users)
$recent_users = [];
$stmt = $conn->query("SELECT id, name, role, 
        CASE 
            WHEN role='provider' THEN provider_status 
            ELSE status 
        END AS status 
        FROM users ORDER BY created_at DESC LIMIT 5");
if ($stmt) {
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Dashboard</h2>
<p>An overview of platform activity and key metrics.</p>

<!-- Stat Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(39, 174, 96, 0.1); color: #27ae60;">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h4>Total Revenue</h4>
            <p>$<?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1); color: #3498db;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h4>Active Users</h4>
            <p><?php echo $active_users; ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: #f39c12;">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-info">
            <h4>Pending Providers</h4>
            <p><?php echo $pending_providers; ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(142, 68, 173, 0.1); color: #8e44ad;">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h4>New Messages</h4>
            <p><?php echo $new_messages; ?></p>
        </div>
    </div>
</div>

<!-- Quick Access -->
<div class="dashboard-section">
    <h3>Quick Access</h3>
    <div class="quick-access-grid">
        <a href="manage_providers.php" class="access-card">
            <i class="fas fa-user-check"></i>
            <span>Provider Approvals</span>
        </a>
        <a href="manage_users.php" class="access-card">
            <i class="fas fa-users-cog"></i>
            <span>User Management</span>
        </a>
        <a href="reports.php" class="access-card">
            <i class="fas fa-chart-bar"></i>
            <span>Platform Reports</span>
        </a>
        <a href="resolve_disputes.php" class="access-card">
            <i class="fas fa-gavel"></i>
            <span>Resolve Disputes</span>
        </a>
    </div>
</div>

<!-- Recent Registrations -->
<div class="dashboard-section">
    <h3>Recent Registrations</h3>
    <div class="content-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_users)): ?>
                        <?php foreach($recent_users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                            <td>
                                <?php 
                                    $status_class = strtolower($user['status']);
                                    echo "<span class='status-badge status-{$status_class}'>" . htmlspecialchars($user['status']) . "</span>";
                                ?>
                            </td>
                            <td>
                                <a href="<?php echo ($user['role'] === 'provider') ? 'manage_providers.php' : 'manage_users.php'; ?>" class="btn-view">
                                    View
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No recent registrations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
