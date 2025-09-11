<?php
// settings.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all settings from the database
$settings_db = [];
$stmt = $conn->prepare("SELECT setting_key, setting_value FROM settings");
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings_db[$row['setting_key']] = $row['setting_value'];
}

// Populate form values with fetched settings or defaults
$settings = [
    'homepage_welcome_message' => $settings_db['homepage_welcome_message'] ?? 'Welcome to Innovista! Your one-stop solution for interior design and restoration services.',
    'homepage_about_text'      => $settings_db['homepage_about_text']      ?? 'Innovista is a premier platform connecting skilled interior designers and restoration experts with clients seeking quality and reliability.',
    'platform_name'            => $settings_db['platform_name']            ?? 'Innovista',
    'admin_contact_email'      => $settings_db['admin_contact_email']      ?? 'contact@innovista.com',
    'platform_address'         => $settings_db['platform_address']         ?? '123 Design Lane, Jaffna, Sri Lanka',
    'facebook_url'             => $settings_db['facebook_url']             ?? '', // Default empty if not set
    'instagram_url'            => $settings_db['instagram_url']            ?? '', // Default empty if not set
];

?>

<h2>System Settings & Homepage Content</h2>
<p>Manage global settings for the Innovista platform.</p>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<form action="save_settings.php" method="POST">
    <div class="content-card">
        <h3>Homepage Content</h3>
        <div class="form-group">
            <label for="homepage_welcome_message">Welcome Message (Main Heading)</label>
            <input type="text" id="homepage_welcome_message" name="homepage_welcome_message" value="<?php echo htmlspecialchars($settings['homepage_welcome_message']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_about_text">About Us Text</label>
            <textarea id="homepage_about_text" name="homepage_about_text" rows="4"><?php echo htmlspecialchars($settings['homepage_about_text']); ?></textarea>
        </div>
    </div>

    <div class="content-card">
        <h3>Platform Information</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="platform_name">Platform Name</label>
                <input type="text" id="platform_name" name="platform_name" value="<?php echo htmlspecialchars($settings['platform_name']); ?>">
            </div>
             <div class="form-group">
                <label for="admin_contact_email">Admin Contact Email</label>
                <input type="email" id="admin_contact_email" name="admin_contact_email" value="<?php echo htmlspecialchars($settings['admin_contact_email']); ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="platform_address">Company Address</label>
            <input type="text" id="platform_address" name="platform_address" value="<?php echo htmlspecialchars($settings['platform_address']); ?>">
        </div>
    </div>

    <div class="content-card">
        <h3>Social Media Links</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="facebook_url">Facebook URL</label>
                <input type="url" id="facebook_url" name="facebook_url" placeholder="https://facebook.com/innovista" value="<?php echo htmlspecialchars($settings['facebook_url']); ?>">
            </div>
             <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" placeholder="https://instagram.com/innovista" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>">
            </div>
        </div>
    </div>

    <button type="submit" class="btn-save">Save All Settings</button>
</form>

<?php require_once 'admin_footer.php'; ?>