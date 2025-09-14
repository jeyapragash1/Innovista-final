<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\my_profile.php

require_once '../public/session.php';
require_once '../handlers/flash_message.php';

// --- User-specific authentication function ---
if (!function_exists('protectPage')) {
    function protectPage(string $requiredRole): void {
        if (!isUserLoggedIn()) {
            header("Location: ../public/login.php");
            exit();
        }
        if (getUserRole() !== $requiredRole && getUserRole() !== 'admin') { 
            set_flash_message('error', 'Access denied. You do not have permission to view this page.');
            header("Location: ../public/index.php");
            exit();
        }
    }
}
protectPage('customer');

$pageTitle = 'My Profile';
require_once '../includes/user_dashboard_header.php';
require_once '../config/Database.php';

$customer_id = getUserId();
$database = new Database();
$conn = $database->getConnection();

$currentUser = null;
$message = '';
$status_type = '';

// Fetch current user data (including profile_image_path for display)
try {
    $stmt = $conn->prepare("SELECT id, name, email, phone, address, bio, profile_image_path FROM users WHERE id = :id");
    $stmt->bindParam(':id', $customer_id, PDO::PARAM_INT);
    $stmt->execute();
    $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$currentUser) {
        set_flash_message('error', 'Your user account could not be found. Please contact support.');
        header("Location: ../public/logout.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Error fetching user profile data: " . $e->getMessage());
    $message = "Error loading your profile. Please try again.";
    $status_type = "error";
}

// Display messages that came from handlers
if (isset($_GET['status']) && isset($_GET['message'])) {
    $message = htmlspecialchars($_GET['message']);
    $status_type = htmlspecialchars($_GET['status']);
}

?>

<?php display_flash_message(); ?>

<h2>Manage My Profile</h2>
<p>Update your personal information and change your password.</p>

<div class="content-card">
    <h3>Personal Information</h3>
    <form action="../handlers/handle_update_profile.php" method="POST" class="form-section" enctype="multipart/form-data">
        <div class="profile-header-edit text-center mb-4">
            <img src="<?php echo getImageSrc($currentUser['profile_image_path'] ?? 'assets/images/default-avatar.jpg'); ?>" 
                 alt="Profile Avatar" class="profile-avatar-lg mb-3">
            <div class="form-group">
                <label for="profile_image" class="btn btn-secondary btn-sm">Upload New Image</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                <small class="d-block text-muted mt-2">Max file size: 2MB. JPG, PNG, GIF. Leave blank to keep current.</small>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="address">Primary Address</label>
            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($currentUser['address'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" rows="5" placeholder="Tell us a little about yourself..."><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
        </div>

        <button type="submit" name="update_details" class="btn-submit">Save Changes</button>
    </form>
</div>

<div class="content-card mt-4">
    <h3>Change Password</h3>
    <form action="../handlers/handle_update_password.php" method="POST" class="form-section">
        <div class="form-grid">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_new_password">Confirm New Password</label>
                <input type="password" id="confirm_new_password" name="confirm_new_password" required>
            </div>
        </div>
        <button type="submit" name="update_password" class="btn-submit">Update Password</button>
    </form>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>

<!-- Add some basic styling to public/assets/css/dashboard.css or main.css -->
<style>
    /* These styles should be moved to your CSS file (e.g., public/assets/css/dashboard.css) */
    .profile-header-edit {
        text-align: center;
        margin-bottom: 2rem;
    }
    .profile-avatar-lg {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color, #0d9488); /* Using CSS variable for primary color */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
    .profile-header-edit .btn {
        margin-top: 0.5rem;
    }
</style>