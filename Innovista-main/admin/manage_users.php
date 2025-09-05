<?php 
require_once 'admin_header.php'; 
require_once '../config/Database.php';

session_start();

// DB connection
$db = (new Database())->getConnection();

// Fetch all users
$stmt = $db->prepare("SELECT id, name, email, role, status FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>User Management</h2>
<p>View all customers and service providers. You can deactivate accounts to block access.</p>

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
                                <span class="status-badge <?php echo strtolower($user['status']) == 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo htmlspecialchars($user['status']); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <?php if (strtolower($user['status']) == 'active'): ?>
                                    <a href="deactivate_user.php?id=<?php echo $user['id']; ?>" class="btn-link" style="color: var(--status-rejected);">Deactivate</a>
                                <?php else: ?>
                                    <a href="activate_user.php?id=<?php echo $user['id']; ?>" class="btn-link" style="color: var(--status-approved);">Activate</a>
                                <?php endif; ?>
                                
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-action edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-action delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
