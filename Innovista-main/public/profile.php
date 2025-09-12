<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\public\profile.php

$pageTitle = 'Edit Profile';
include 'header.php'; // Includes session_start() and helper functions

// Redirect if user is not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Establish DB connection
require_once '../config/Database.php';
$db = new Database();
$conn = $db->getConnection();

$loggedInUserId = getUserId(); // Get the ID of the currently logged-in user
$user = null; // Initialize user data

$message = '';
$status_type = '';

// Fetch current user data
try {
    $stmt = $conn->prepare("SELECT id, name, email, phone, address, bio, profile_image_path, role FROM users WHERE id = :id");
    $stmt->bindParam(':id', $loggedInUserId, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // This shouldn't happen if isUserLoggedIn() is true, but good for safety
        header("Location: logout.php"); // Log out user if their record is missing
        exit();
    }
} catch (PDOException $e) {
    error_log("Profile page error fetching user data: " . $e->getMessage());
    $message = "Error loading profile data. Please try again.";
    $status_type = "error";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Basic validation
    if (empty($name) || empty($email)) {
        $message = "Name and Email are required.";
        $status_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $status_type = "error";
    } else {
        // Image Upload Handling
        $new_profile_image_path = $user['profile_image_path']; // Default to current image
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/profiles/'; // Relative to public/
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_tmp = $_FILES['profile_image']['tmp_name'];
            $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed_ext)) {
                // Generate a unique filename
                $new_file_name = 'user_' . $loggedInUserId . '_' . uniqid() . '.' . $file_ext;
                $destination_path = $upload_dir . $new_file_name;

                if (move_uploaded_file($file_tmp, $destination_path)) {
                    // Delete old image if it was an uploaded one (not a URL or default)
                    if ($user['profile_image_path'] && 
                        !filter_var($user['profile_image_path'], FILTER_VALIDATE_URL) &&
                        $user['profile_image_path'] !== 'assets/images/default-avatar.jpg' &&
                        file_exists($user['profile_image_path'])) { // Path is relative to public/
                        unlink($user['profile_image_path']);
                    }
                    $new_profile_image_path = $destination_path; // Store path relative to public/
                } else {
                    $message = "Failed to move uploaded image.";
                    $status_type = "error";
                }
            } else {
                $message = "Invalid image file type. Only JPG, JPEG, PNG, GIF allowed.";
                $status_type = "error";
            }
        }

        if (empty($message)) { // Proceed to update DB only if no prior errors
            try {
                $stmt_update = $conn->prepare("
                    UPDATE users SET 
                        name = :name, 
                        email = :email, 
                        phone = :phone, 
                        address = :address, 
                        bio = :bio, 
                        profile_image_path = :profile_image_path
                    WHERE id = :id
                ");
                $stmt_update->bindParam(':name', $name);
                $stmt_update->bindParam(':email', $email);
                $stmt_update->bindParam(':phone', $phone);
                $stmt_update->bindParam(':address', $address);
                $stmt_update->bindParam(':bio', $bio);
                $stmt_update->bindParam(':profile_image_path', $new_profile_image_path);
                $stmt_update->bindParam(':id', $loggedInUserId, PDO::PARAM_INT);
                $stmt_update->execute();

                if ($stmt_update->rowCount() > 0) {
                    $message = "Profile updated successfully!";
                    $status_type = "success";
                    // Update session name if changed
                    $_SESSION['user_name'] = $name;
                } else {
                    $message = "No changes made to profile.";
                    $status_type = "info";
                }
                // Re-fetch user data to display updated info immediately
                $stmt = $conn->prepare("SELECT id, name, email, phone, address, bio, profile_image_path, role FROM users WHERE id = :id");
                $stmt->bindParam(':id', $loggedInUserId, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

            } catch (PDOException $e) {
                // Check for duplicate email error specifically
                if ($e->getCode() == '23000' && str_contains($e->getMessage(), 'email')) {
                    $message = "This email is already registered.";
                } else {
                    $message = "Database error: " . $e->getMessage();
                }
                $status_type = "error";
                error_log("User profile update error: " . $e->getMessage());
            }
        }
    }
}
?>

<div class="container page-section">
    <h2 class="section-title">Edit Your Profile</h2>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo htmlspecialchars($status_type); ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if ($user): ?>
    <div class="content-card">
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="profile-header-edit text-center mb-4">
                <img src="<?php echo getImageSrc($user['profile_image_path'] ?? 'assets/images/default-avatar.jpg'); ?>" 
                     alt="Profile Avatar" class="profile-avatar-lg mb-3">
                <div class="form-group">
                    <label for="profile_image" class="btn btn-secondary btn-sm">Upload New Image</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                    <small class="d-block text-muted mt-2">Max file size: 2MB. JPG, PNG, GIF. Leave blank to keep current.</small>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group mt-3">
                <label for="bio">Bio (Tell us about yourself)</label>
                <textarea id="bio" name="bio" rows="5" placeholder="A short description about yourself or your company..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            </div>

            <div class="action-buttons mt-4">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
    </div>
    <?php else: ?>
        <p class="text-center">Unable to load profile data.</p>
    <?php endif; ?>
</div>

<?php include './footer.php'; ?>

<!-- Add some basic styling to public/assets/css/dashboard.css or main.css -->
<style>
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