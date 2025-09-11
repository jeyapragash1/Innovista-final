<?php
// manage_users.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

// DB connection
$db = (new Database())->getConnection();

// Fetch all users, excluding the current admin user
$current_admin_id = $_SESSION['user_id']; // Get current admin's ID from session

$stmt = $db->prepare("SELECT id, name, email, role, status, provider_status FROM users WHERE id != :current_admin_id ORDER BY created_at DESC");
$stmt->bindParam(':current_admin_id', $current_admin_id, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>User Management</h2>
<p>View all customers and service providers. You can deactivate accounts to block access.</p>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td>
                                <?php
                                $display_status = ($user['role'] === 'provider') ? $user['provider_status'] : $user['status'];
                                $status_class = strtolower($display_status);
                                if ($user['role'] === 'provider') {
                                    $status_class = (strtolower($display_status) === 'pending') ? 'pending' : (strtolower($display_status) === 'approved' ? 'active' : 'inactive');
                                }
                                ?>
                                <span class="status-badge status-<?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($display_status); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <?php if ($user['status'] == 'active'): ?>
                                    <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=deactivate" class="btn-link delete" onclick="return confirm('Are you sure you want to DEACTIVATE <?php echo htmlspecialchars($user['name']); ?>?');">Deactivate</a>
                                <?php else: ?>
                                    <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=activate" class="btn-link" onclick="return confirm('Are you sure you want to ACTIVATE <?php echo htmlspecialchars($user['name']); ?>?');">Activate</a>
                                <?php endif; ?>
                                
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=delete" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to DELETE <?php echo htmlspecialchars($user['name']); ?>? This action cannot be undone.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No users found (excluding current admin).</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>