<?php 
// admin_dashboard.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

// Initialize DB
$db = new Database();
$conn = $db->getConnection();

// --- FETCH DATA FOR DASHBOARD ---

// 1. Total Revenue (sum of all payments)
$total_revenue = 0;
$stmt = $conn->prepare("SELECT SUM(amount) as revenue FROM payments");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_revenue = $row['revenue'] ?? 0;

// 2. Active Users (customers + providers with status='active', excluding admins)
$active_users = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE status='active' AND role != 'admin'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$active_users = $row['total'];

// 3. Pending Providers
$pending_providers = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role='provider' AND provider_status='pending'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$pending_providers = $row['total'];

// 4. New Messages (contacts table, last 24 hours) - or you can use is_read = 0 if you add that column
$new_messages = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM contacts WHERE created_at >= NOW() - INTERVAL 1 DAY");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$new_messages = $row['total'];


// 5. Recent Registrations (latest 5 users, excluding admins)
$recent_users = [];
$stmt = $conn->prepare("SELECT id, name, role, 
        CASE 
            WHEN role='provider' THEN provider_status 
            ELSE status 
        END AS status 
        FROM users 
        WHERE role != 'admin' -- Exclude admin users from recent registrations
        ORDER BY created_at DESC LIMIT 5");
$stmt->execute();
$recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: Fetch data for Open Disputes count
$open_disputes_count = 0;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM disputes WHERE status != 'resolved'");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$open_disputes_count = $row['total'];

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
     <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(231, 76, 60, 0.1); color: #e74c3c;">
            <i class="fas fa-gavel"></i>
        </div>
        <div class="stat-info">
            <h4>Open Disputes</h4>
            <p><?php echo $open_disputes_count; ?></p>
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
         <a href="manage_contacts.php" class="access-card">
            <i class="fas fa-envelope-open-text"></i>
            <span>Contact Messages</span>
        </a>
         <a href="settings.php" class="access-card">
            <i class="fas fa-cogs"></i>
            <span>System Settings</span>
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
                                    // Map custom provider_status to generic classes if needed, e.g., 'pending' to 'status-pending'
                                    if ($user['role'] === 'provider') {
                                        $status_class = (strtolower($user['status']) === 'pending') ? 'pending' : (strtolower($user['status']) === 'approved' ? 'active' : 'inactive');
                                    }
                                    echo "<span class='status-badge status-{$status_class}'>" . htmlspecialchars($user['status']) . "</span>";
                                ?>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'provider'): ?>
                                    <a href="manage_providers.php" class="btn-view">View</a>
                                <?php elseif ($user['role'] === 'customer'): ?>
                                    <a href="manage_users.php" class="btn-view">View</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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