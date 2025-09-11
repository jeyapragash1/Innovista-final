<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\admin\edit_user.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

$user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user = null;

if (!$user_id || !is_numeric($user_id)) {
    header("Location: manage_users.php?status=error&message=Invalid user ID.");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, phone, address, bio, portfolio, profile_image_path FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage_users.php?status=error&message=User not found.");
    exit();
}

// Prevent admin from editing themselves in this generic user edit page to avoid conflicts
if ((int)$user_id === (int)$_SESSION['user_id']) {
    header("Location: manage_users.php?status=error&message=You cannot edit your own account from here. Use System Settings for admin details.");
    exit();
}

$message = '';
$status_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $provider_status = filter_input(INPUT_POST, 'provider_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $credentials_verified = filter_input(INPUT_POST, 'credentials_verified', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Image Upload Handling (for profile_image_path)
    $new_profile_image_path = $user['profile_image_path']; // Default to current image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/profiles/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'user_' . $user_id . '_' . uniqid() . '.' . $file_ext;
            $destination_path = $upload_dir . $new_file_name;
            $public_image_path_for_db = 'uploads/profiles/' . $new_file_name;

            if (move_uploaded_file($file_tmp, $destination_path)) {
                // Delete old image if it was an uploaded one
                if ($user['profile_image_path'] && 
                    !filter_var($user['profile_image_path'], FILTER_VALIDATE_URL) &&
                    $user['profile_image_path'] !== 'assets/images/default-avatar.jpg' &&
                    file_exists('../public/' . $user['profile_image_path'])) {
                    unlink('../public/' . $user['profile_image_path']);
                }
                $new_profile_image_path = $public_image_path_for_db;
            } else {
                $message = "Failed to move uploaded image.";
                $status_type = "error";
            }
        } else {
            $message = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed.";
            $status_type = "error";
        }
    }

    if (empty($message)) { // Only proceed if no image upload error
        try {
            $update_query = "UPDATE users SET name = :name, email = :email, role = :role, status = :status, phone = :phone, address = :address, bio = :bio, profile_image_path = :profile_image_path";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':role' => $role,
                ':status' => $status,
                ':phone' => $phone,
                ':address' => $address,
                ':bio' => $bio,
                ':profile_image_path' => $new_profile_image_path,
                ':id' => $user_id
            ];

            if ($role === 'provider') {
                $update_query .= ", provider_status = :provider_status, credentials_verified = :credentials_verified";
                $params[':provider_status'] = $provider_status;
                $params[':credentials_verified'] = $credentials_verified;
            } else {
                // If role changes from provider to customer/admin, unset provider-specific statuses
                $update_query .= ", provider_status = NULL, credentials_verified = 'no'";
            }

            $update_query .= " WHERE id = :id";
            $stmt = $conn->prepare($update_query);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                $message = "User updated successfully.";
                $status_type = "success";
            } else {
                $message = "No changes made or failed to update user.";
                $status_type = "info";
            }
            // Re-fetch user data to display updated info
            $stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, phone, address, bio, portfolio, profile_image_path FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $status_type = "error";
            error_log("Edit User Error: " . $e->getMessage());
        }
    }
}
?>

<h2>Edit User: <?php echo htmlspecialchars($user['name']); ?></h2>

<?php
if (isset($message) && $message !== '') {
    echo "<div class='alert alert-{$status_type}'>" . htmlspecialchars($message) . "</div>";
}
?>

<div class="content-card">
    <form action="edit_user.php?id=<?php echo htmlspecialchars($user_id); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
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
                <select id="role" name="role" required onchange="toggleProviderFields()">
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
            
            <div id="provider-fields" style="display: <?php echo ($user['role'] === 'provider') ? 'grid' : 'none'; ?>; grid-column: 1 / -1; gap: 1.5rem; grid-template-columns: inherit;">
                <div class="form-group">
                    <label for="provider_status">Provider Approval Status</label>
                    <select id="provider_status" name="provider_status">
                        <option value="pending" <?php echo ($user['provider_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo ($user['provider_status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo ($user['provider_status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="credentials_verified">Credentials Verified?</label>
                    <select id="credentials_verified" name="credentials_verified">
                        <option value="no" <?php echo ($user['credentials_verified'] === 'no') ? 'selected' : ''; ?>>No</option>
                        <option value="yes" <?php echo ($user['credentials_verified'] === 'yes') ? 'selected' : ''; ?>>Yes</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
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
        
        <div class="form-group mt-3">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">
            <?php if (!empty($user['profile_image_path'])): ?>
                <p>Current Image:</p>
                <img src="<?php echo getImageSrc($user['profile_image_path']); ?>" alt="Profile Image" style="max-width: 150px; height: auto; margin-top: 10px; border-radius: 8px;">
            <?php endif; ?>
        </div>

        <div class="action-buttons mt-4">
            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="manage_users.php" class="btn-link">Back to User Management</a>
        </div>
    </form>
</div>

<script>
    function toggleProviderFields() {
        const roleSelect = document.getElementById('role');
        const providerFields = document.getElementById('provider-fields');
        if (roleSelect.value === 'provider') {
            providerFields.style.display = 'grid';
        } else {
            providerFields.style.display = 'none';
        }
    }
    // Call on page load to ensure correct initial state
    document.addEventListener('DOMContentLoaded', toggleProviderFields);
</script>

<?php require_once 'admin_footer.php'; ?>