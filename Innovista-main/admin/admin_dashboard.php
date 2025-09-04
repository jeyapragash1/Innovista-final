<?php 
require_once 'admin_header.php'; 

// --- PHP DATA PREPARATION FOR DASHBOARD ---
// In a real application, this data would come from your database
$stats = [
    'total_revenue' => 27100.00,
    'active_users' => 13,
    'pending_providers' => 2,
    'new_messages' => 11
];

// Data for "Recent Registrations" Table
$recent_users = [
    ['name' => 'Creative Homes', 'role' => 'Provider', 'status' => 'Pending', 'link' => 'manage_providers.php'],
    ['name' => 'Alice Johnson', 'role' => 'Customer', 'status' => 'Active', 'link' => 'manage_users.php'],
    ['name' => 'Urban Crafters', 'role' => 'Provider', 'status' => 'Approved', 'link' => 'manage_providers.php'],
    ['name' => 'Bob Williams', 'role' => 'Customer', 'status' => 'Active', 'link' => 'manage_users.php']
];

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
            <p>$<?php echo number_format($stats['total_revenue'], 2); ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1); color: #3498db;">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h4>Active Users</h4>
            <p><?php echo $stats['active_users']; ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: #f39c12;">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="stat-info">
            <h4>Pending Providers</h4>
            <p><?php echo $stats['pending_providers']; ?></p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(142, 68, 173, 0.1); color: #8e44ad;">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h4>New Messages</h4>
            <p><?php echo $stats['new_messages']; ?></p>
        </div>
    </div>
</div>

<!-- NEW: Quick Access Action Hub -->
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

<!-- NEW: Recent Registrations Table -->
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
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <?php 
                                    $status_class = strtolower($user['status']);
                                    echo "<span class='status-badge status-{$status_class}'>" . htmlspecialchars($user['status']) . "</span>";
                                ?>
                            </td>
                            <td><a href="<?php echo htmlspecialchars($user['link']); ?>" class="btn-view">View</a></td>
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