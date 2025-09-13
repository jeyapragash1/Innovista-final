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

// Fetch user data - ensure to select all potentially used columns
$stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, credentials_verified, phone, address, bio, portfolio, profile_image_path FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: manage_users.php?status=error&message=User not found.");
    exit();
}

// Prevent admin from editing themselves in this generic user edit page to avoid conflicts
// Admins should manage their own profile details through a dedicated "System Settings" or "Admin Profile" page.
if ((int)$user_id === (int)$_SESSION['user_id']) {
    header("Location: manage_users.php?status=error&message=You cannot edit your own account from here. Use System Settings for admin details.");
    exit();
}

$message = '';
$status_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin can ONLY edit these specific fields via this form: role, status, provider_status, credentials_verified.
    // Other fields (name, email, phone, address, bio, profile_image) are read-only and will NOT be processed for update here.
    
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
    // Provider-specific fields: only get if the submitted role is 'provider'
    $provider_status = ($role === 'provider') ? (filter_input(INPUT_POST, 'provider_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? null) : null;
    $credentials_verified = ($role === 'provider') ? (filter_input(INPUT_POST, 'credentials_verified', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'no') : 'no';

    // No image upload handling here, as admin isn't meant to change user images via this form.
    // The profile_image_path will not be updated from this form.

    // Basic validation for the editable fields
    if (empty($role) || empty($status)) {
        $message = "Role and Account Status are required.";
        $status_type = "error";
    } else {
        try {
            // FIX: UPDATE query now only targets role, status, provider_status, and credentials_verified
            $update_query = "UPDATE users SET role = :role, status = :status";
            $params = [
                ':role' => $role,
                ':status' => $status,
                ':id' => $user_id
            ];

            if ($role === 'provider') {
                $update_query .= ", provider_status = :provider_status, credentials_verified = :credentials_verified";
                $params[':provider_status'] = $provider_status;
                $params[':credentials_verified'] = $credentials_verified;
            } else {
                // If role changes from provider to customer/admin, or if it was never provider,
                // ensure provider-specific statuses are NULL/default.
                $update_query .= ", provider_status = NULL, credentials_verified = 'no'";
            }

            $update_query .= " WHERE id = :id";
            $stmt = $conn->prepare($update_query);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                $message = "User role and status updated successfully.";
                $status_type = "success";
            } else {
                $message = "No changes made or failed to update user.";
                $status_type = "info";
            }
            // Re-fetch user data to display updated info
            $stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, credentials_verified, phone, address, bio, portfolio, profile_image_path FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC); // Update $user array with new data

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
                <!-- FIX: Make readonly -->
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <!-- FIX: Make readonly -->
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <!-- This field remains editable -->
                <select id="role" name="role" required onchange="toggleProviderFields()">
                    <option value="customer" <?php echo ($user['role'] === 'customer') ? 'selected' : ''; ?>>Customer</option>
                    <option value="provider" <?php echo ($user['role'] === 'provider') ? 'selected' : ''; ?>>Provider</option>
                    <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Account Status</label>
                <!-- This field remains editable -->
                <select id="status" name="status" required>
                    <option value="active" <?php echo ($user['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($user['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div id="provider-fields" style="display: <?php echo ($user['role'] === 'provider') ? 'grid' : 'none'; ?>; grid-column: 1 / -1; gap: 1.5rem; grid-template-columns: inherit;">
                <div class="form-group">
                    <label for="provider_status">Provider Approval Status</label>
                    <select id="provider_status" name="provider_status">
                        <!-- Use null coalescing operator to safely access provider_status -->
                        <option value="pending" <?php echo (($user['provider_status'] ?? '') === 'pending') ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo (($user['provider_status'] ?? '') === 'approved') ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo (($user['provider_status'] ?? '') === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="credentials_verified">Credentials Verified?</label>
                    <select id="credentials_verified" name="credentials_verified">
                        <!-- Use null coalescing operator to safely access credentials_verified -->
                        <option value="no" <?php echo (($user['credentials_verified'] ?? '') === 'no') ? 'selected' : ''; ?>>No</option>
                        <option value="yes" <?php echo (($user['credentials_verified'] ?? '') === 'yes') ? 'selected' : ''; ?>>Yes</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="phone">Phone</label>
            <!-- FIX: Make readonly -->
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <!-- FIX: Make readonly -->
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <!-- FIX: Make readonly -->
            <textarea id="bio" name="bio" rows="4" readonly><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>
        
        <div class="form-group mt-3">
            <label for="profile_image">Profile Image</label>
            <!-- FIX: Make file input disabled -->
            <input type="file" id="profile_image" name="profile_image" accept="image/*" disabled>
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