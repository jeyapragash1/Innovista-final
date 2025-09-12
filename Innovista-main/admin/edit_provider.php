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
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $provider_status = filter_input(INPUT_POST, 'provider_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $credentials_verified = filter_input(INPUT_POST, 'credentials_verified', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Image Upload Handling
    $new_profile_image_path = $provider_data['profile_image_path']; // Default to current image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../public/uploads/profiles/'; // Publicly accessible path for profile images
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create directory if it doesn't exist
        }
        $file_tmp = $_FILES['profile_image']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_ext, $allowed_ext)) {
            $new_file_name = 'provider_' . $provider_id . '_' . uniqid() . '.' . $file_ext;
            $destination_path = $upload_dir . $new_file_name;
            $public_image_path_for_db = 'uploads/profiles/' . $new_file_name; // Path stored in DB (relative to public/)

            if (move_uploaded_file($file_tmp, $destination_path)) {
                // Delete old image if it was an uploaded one (not a URL or default avatar)
                if ($provider_data['profile_image_path'] && 
                    !filter_var($provider_data['profile_image_path'], FILTER_VALIDATE_URL) &&
                    $provider_data['profile_image_path'] !== 'assets/images/default-avatar.jpg' &&
                    file_exists('../public/' . $provider_data['profile_image_path'])) { // Path relative to admin folder
                    unlink('../public/' . $provider_data['profile_image_path']);
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

    if (empty($message)) { // Only proceed with DB update if no image upload error
        try {
            $stmt_update = $conn->prepare("
                UPDATE users SET 
                    name = :name, 
                    email = :email, 
                    phone = :phone, 
                    address = :address, 
                    bio = :bio, 
                    provider_status = :provider_status, 
                    credentials_verified = :credentials_verified,
                    profile_image_path = :profile_image_path
                WHERE id = :id AND role = 'provider'
            ");
            $stmt_update->bindParam(':name', $name);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->bindParam(':phone', $phone);
            $stmt_update->bindParam(':address', $address);
            $stmt_update->bindParam(':bio', $bio);
            $stmt_update->bindParam(':provider_status', $provider_status);
            $stmt_update->bindParam(':credentials_verified', $credentials_verified);
            $stmt_update->bindParam(':profile_image_path', $new_profile_image_path);
            $stmt_update->bindParam(':id', $provider_id, PDO::PARAM_INT);
            $stmt_update->execute();

            if ($stmt_update->rowCount() > 0) {
                $message = "Provider updated successfully.";
                $status_type = "success";
            } else {
                $message = "No changes made or failed to update provider.";
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($provider_data['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($provider_data['email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($provider_data['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($provider_data['address'] ?? ''); ?>">
            </div>
             <div class="form-group">
                <label for="provider_status">Approval Status</label>
                <select id="provider_status" name="provider_status" required>
                    <option value="pending" <?php echo ($provider_data['provider_status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo ($provider_data['provider_status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo ($provider_data['provider_status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="form-group">
                <label for="credentials_verified">Credentials Verified?</label>
                <select id="credentials_verified" name="credentials_verified" required>
                    <option value="no" <?php echo ($provider_data['credentials_verified'] === 'no') ? 'selected' : ''; ?>>No</option>
                    <option value="yes" <?php echo ($provider_data['credentials_verified'] === 'yes') ? 'selected' : ''; ?>>Yes</option>
                </select>
            </div>
        </div>

        <div class="form-group mt-3">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="6"><?php echo htmlspecialchars($provider_data['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-group mt-3">
            <label for="profile_image">Profile Image</label>
            <input type="file" id="profile_image" name="profile_image" accept="image/*">
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