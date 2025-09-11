<?php
// edit_user.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

$user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user = null;

if (!$user_id || !is_numeric($user_id)) {
    header("Location: manage_users.php?status=error&message=Invalid user ID.");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, phone, address, bio, portfolio FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage_users.php?status=error&message=User not found.");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $provider_status = filter_input(INPUT_POST, 'provider_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


    // Basic validation
    if (empty($name) || empty($email) || empty($role) || empty($status)) {
        $message = "All fields are required.";
        $status_type = "error";
    } else {
        try {
            $update_query = "UPDATE users SET name = :name, email = :email, role = :role, status = :status, phone = :phone, address = :address, bio = :bio";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':status' => $status,
                ':phone' => $phone,
                ':address' => $address,
                ':bio' => $bio,
                ':id' => $user_id
            ];

            if ($role === 'provider') {
                $update_query .= ", provider_status = :provider_status";
                $params[':provider_status'] = $provider_status;
            }

            $update_query .= " WHERE id = :id";
            $stmt = $conn->prepare($update_query);
            $stmt->execute($params);

            $message = "User updated successfully.";
            $status_type = "success";

            // Re-fetch user data to display updated info
            $stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, phone, address, bio, portfolio FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $status_type = "error";
        }
    }
}
?>

<h2>Edit User: <?php echo htmlspecialchars($user['name']); ?></h2>

<?php
if (isset($message)) {
    echo "<div class='alert alert-{$status_type}'>" . htmlspecialchars($message) . "</div>";
}
?>

<div class="content-card">
    <form action="edit_user.php?id=<?php echo htmlspecialchars($user_id); ?>" method="POST">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="customer" <?php echo ($user['role'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
                <option value="provider" <?php echo ($user['role'] === 'provider') ? 'selected' : ''; ?>>Provider</option>
                <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Account Status</label>
            <select id="status" name="status" required>
                <option value="active" <?php echo ($user['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($user['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        
        <?php if ($user['role'] === 'provider'): ?>
        <div class="form-group">
            <label for="provider_status">Provider Approval Status</label>
            <select id="provider_status" name="provider_status">
                <option value="pending" <?php echo ($user['provider_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo ($user['provider_status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="rejected" <?php echo ($user['provider_status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>
        <!-- You might want to allow editing portfolio directly or link to a separate page -->
        
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="manage_users.php" class="btn-link">Back to User Management</a>
    </form>
</div>

<?php require_once 'admin_footer.php'; ?>