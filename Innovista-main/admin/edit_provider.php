<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\admin\edit_provider.php
require_once 'admin_header.php';
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

$provider_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$provider_data = null;

if (!$provider_id || !is_numeric($provider_id)) {
    header("Location: manage_providers.php?status=error&message=Invalid provider ID.");
    exit();
}

// Fetch provider data (including profile_image_path)
// Fetch all relevant fields, even if not editable, for display
$stmt = $conn->prepare("SELECT id, name, email, phone, address, bio, provider_status, credentials_verified, profile_image_path FROM users WHERE id = :id AND role = 'provider'");
$stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
$stmt->execute();
$provider_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider_data) {
    header("Location: manage_providers.php?status=error&message=Provider not found.");
    exit();
}

$message = '';
$status_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Admin can ONLY edit provider_status and credentials_verified via this form.
    // Other fields are read-only and should not be processed for update here.
    $provider_status = filter_input(INPUT_POST, 'provider_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $credentials_verified = filter_input(INPUT_POST, 'credentials_verified', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // No file upload handling here, as admin isn't meant to change provider images via this form.
    // If you need admin to change images, that would be a separate, more complex form/action.
    // We retain the current image path from $provider_data.
    $new_profile_image_path = $provider_data['profile_image_path']; 

    // No direct validation for name, email, etc., as they are not being updated.
    // Only validate the fields that are actually editable.
    if (empty($provider_status) || empty($credentials_verified)) {
        $message = "Approval Status and Credentials Verified status are required.";
        $status_type = "error";
    } else {
        try {
            // FIX: UPDATE query now only targets provider_status and credentials_verified
            $stmt_update = $conn->prepare("
                UPDATE users SET 
                    provider_status = :provider_status, 
                    credentials_verified = :credentials_verified
                WHERE id = :id AND role = 'provider'
            ");
            $stmt_update->bindParam(':provider_status', $provider_status);
            $stmt_update->bindParam(':credentials_verified', $credentials_verified);
            $stmt_update->bindParam(':id', $provider_id, PDO::PARAM_INT);
            $stmt_update->execute();

            if ($stmt_update->rowCount() > 0) {
                $message = "Provider status and credentials updated successfully.";
                $status_type = "success";
            } else {
                $message = "No changes made to provider status or credentials.";
                $status_type = "info";
            }
            // Re-fetch data to show updated info immediately
            $stmt = $conn->prepare("SELECT id, name, email, phone, address, bio, provider_status, credentials_verified, profile_image_path FROM users WHERE id = :id AND role = 'provider'");
            $stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
            $stmt->execute();
            $provider_data = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $status_type = "error";
            error_log("Edit Provider Error: " . $e->getMessage());
        }
    }
}
?>

<h2>Edit Provider: <?php echo htmlspecialchars($provider_data['name']); ?></h2>

<?php
if (isset($message) && $message !== '') {
    echo "<div class='alert alert-{$status_type}'>" . htmlspecialchars($message) . "</div>";
}
?>

<div class="content-card">
    <form action="edit_provider.php?id=<?php echo htmlspecialchars($provider_data['id']); ?>" method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Company/Provider Name</label>
                <!-- FIX: Make readonly -->
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($provider_data['name']); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <!-- FIX: Make readonly -->
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($provider_data['email']); ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <!-- FIX: Make readonly -->
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($provider_data['phone'] ?? ''); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <!-- FIX: Make readonly -->
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($provider_data['address'] ?? ''); ?>" readonly>
            </div>
             <div class="form-group">
                <label for="provider_status">Approval Status</label>
                <!-- This field remains editable -->
                <select id="provider_status" name="provider_status" required>
                    <option value="pending" <?php echo ($provider_data['provider_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo ($provider_data['provider_status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo ($provider_data['provider_status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="form-group">
                <label for="credentials_verified">Credentials Verified?</label>
                <!-- This field remains editable -->
                <select id="credentials_verified" name="credentials_verified" required>
                    <option value="no" <?php echo ($provider_data['credentials_verified'] === 'no') ? 'selected' : ''; ?>>No</option>
                    <option value="yes" <?php echo ($provider_data['credentials_verified'] === 'yes') ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="bio">Bio</label>
            <!-- FIX: Make readonly -->
            <textarea id="bio" name="bio" rows="6" readonly><?php echo htmlspecialchars($provider_data['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-group mt-3">
            <label for="profile_image">Profile Image</label>
            <!-- FIX: Make file input disabled as admin isn't meant to change it here -->
            <input type="file" id="profile_image" name="profile_image" accept="image/*" disabled>
            <?php if (!empty($provider_data['profile_image_path'])): ?>
                <p>Current Image:</p>
                <img src="<?php echo getImageSrc($provider_data['profile_image_path']); ?>" alt="Profile Image" style="max-width: 150px; height: auto; margin-top: 10px; border-radius: 8px;">
            <?php endif; ?>
        </div>

        <div class="action-buttons mt-4">
            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="view_provider.php?id=<?php echo htmlspecialchars($provider_data['id']); ?>" class="btn-link">Cancel</a>
            <a href="manage_providers.php" class="btn-link">Back to Providers</a>
        </div>
    </form>
</div>

<?php require_once 'admin_footer.php'; ?>