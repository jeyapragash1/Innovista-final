
<?php
require_once '../config/session.php'; 
protectPage('customer');

$pageTitle = 'My Profile';
require_once '../includes/user_dashboard_header.php'; 
require_once '../config/Database.php';

$customer_id = $_SESSION['user_id'];
$database = new Database();
$db = $database->getConnection();
$stmt = $db->prepare("SELECT name, email, phone, address FROM users WHERE id = :id");
$stmt->bindParam(':id', $customer_id);
$stmt->execute();
$currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!-- Flash message for profile update -->
<?php if (function_exists('display_flash_message')): ?>
    <div class="flash-message-container">
        <?php display_flash_message(); ?>
    </div>
<?php endif; ?>

<h2>Manage My Profile</h2>
<p>Update your personal information and change your password.</p>

<div class="content-card">
    <h3>Personal Information</h3>
    <form action="../handlers/handle_update_profile.php" method="POST" class="form-section">
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
        <button type="submit" name="update_details" class="btn-submit">Save Changes</button>
    </form>
</div>

<div class="content-card">
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
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
        </div>
        <button type="submit" name="update_password" class="btn-submit">Update Password</button>
    </form>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>