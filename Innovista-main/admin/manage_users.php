<?php 
// manage_users.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

// DB connection
$db = new Database();
$conn = $db->getConnection(); // $conn now holds the PDO database connection object

// Fetch all users, excluding the current admin user
$current_admin_id = $_SESSION['user_id']; // Get current admin's ID from session

// FIX: Change $db->prepare to $conn->prepare
$stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, profile_image_path FROM users WHERE id != :current_admin_id ORDER BY created_at DESC");
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
                    <th>Image</th>
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
                            <td>
                                <img src="<?php echo getImageSrc($user['profile_image_path'] ?? 'assets/images/default-avatar.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($user['name']); ?>" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                            </td>
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
                                <a href="view_user.php?id=<?php echo $user['id']; ?>" class="btn-link" title="View Details"><i class="fas fa-eye"></i></a>
                                <?php if ($user['status'] == 'active'): ?>
                                    <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=deactivate" class="btn-action delete" title="Deactivate" onclick="return confirm('Are you sure you want to DEACTIVATE <?php echo htmlspecialchars($user['name']); ?>?');"><i class="fas fa-power-off"></i></a>
                                <?php else: ?>
                                    <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=activate" class="btn-action edit" title="Activate" onclick="return confirm('Are you sure you want to ACTIVATE <?php echo htmlspecialchars($user['name']); ?>?');"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="user_actions.php?id=<?php echo $user['id']; ?>&action=delete" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to DELETE <?php echo htmlspecialchars($user['name']); ?>? This action cannot be undone and will delete all associated data.');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No users found (excluding current admin).</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>