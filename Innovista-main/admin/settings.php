<?php
// settings.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all settings from the database
$settings_db = [];
try {
    $stmt = $conn->prepare("SELECT setting_key, setting_value FROM settings");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings_db[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    error_log("Database error fetching settings in settings.php: " . $e->getMessage());
}


// Populate form values with fetched settings or defaults
$settings = [
    'homepage_welcome_message' => $settings_db['homepage_welcome_message'] ?? 'Welcome to Innovista! Your one-stop solution for interior design and restoration services.',
    'homepage_about_text'      => $settings_db['homepage_about_text']      ?? 'Innovista is a premier platform connecting skilled interior designers and restoration experts with clients seeking quality and reliability. Our mission is to simplify the process of creating beautiful spaces.',
    'platform_name'            => $settings_db['platform_name']            ?? 'Innovista',
    'admin_contact_email'      => $settings_db['admin_contact_email']      ?? 'contact@innovista.com',
    'platform_address'         => $settings_db['platform_address']         ?? '123 Design Lane, Jaffna, Sri Lanka',
    'facebook_url'             => $settings_db['facebook_url']             ?? '', // Default empty if not set
    'instagram_url'            => $settings_db['instagram_url']            ?? '', // Default empty if not set
    'homepage_hero_h1'         => $settings_db['homepage_hero_h1']         ?? 'Transforming Spaces, Restoring Dreams',
    'homepage_hero_p'          => $settings_db['homepage_hero_p']          ?? 'Your one-stop platform for interior design, painting, and restoration services in the Northern Province',
    'homepage_how_it_works_title' => $settings_db['homepage_how_it_works_title'] ?? 'How It Works',
    'homepage_services_title'  => $settings_db['homepage_services_title']  ?? 'Our Core Services',
    'homepage_products_title'  => $settings_db['homepage_products_title']  ?? 'Complete Your Project',
    'homepage_products_description' => $settings_db['homepage_products_description'] ?? 'Find high-quality products from trusted brands, all in one place. From paints to furniture, get everything you need for your project delivered.',
    'homepage_why_choose_us_title' => $settings_db['homepage_why_choose_us_title'] ?? 'Why Choose Innovista?',
    'homepage_testimonials_title' => $settings_db['homepage_testimonials_title'] ?? 'What Our Clients Say',
    'homepage_our_work_title' => $settings_db['homepage_our_work_title'] ?? 'Our Recent Work',
    'homepage_our_work_description' => $settings_db['homepage_our_work_description'] ?? 'A glimpse into the spaces we\'ve transformed, showcasing our best projects and diverse expertise.',
    'homepage_faq_title' => $settings_db['homepage_faq_title'] ?? 'Frequently Asked Questions',
    'homepage_cta_title' => $settings_db['homepage_cta_title'] ?? 'Ready to Start Your Next Project?',
    'homepage_cta_description' => $settings_db['homepage_cta_description'] ?? 'Whether you\'re looking to transform your home or grow your service business, the Innovista community is here for you. Join today for a seamless, transparent, and trustworthy experience.',
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
        <h3>Homepage General Content</h3>
        <div class="form-group">
            <label for="homepage_hero_h1">Hero Section Main Heading</label>
            <input type="text" id="homepage_hero_h1" name="homepage_hero_h1" value="<?php echo htmlspecialchars($settings['homepage_hero_h1']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_hero_p">Hero Section Paragraph</label>
            <textarea id="homepage_hero_p" name="homepage_hero_p" rows="3"><?php echo htmlspecialchars($settings['homepage_hero_p']); ?></textarea>
        </div>
         <div class="form-group">
            <label for="homepage_how_it_works_title">"How It Works" Section Title</label>
            <input type="text" id="homepage_how_it_works_title" name="homepage_how_it_works_title" value="<?php echo htmlspecialchars($settings['homepage_how_it_works_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_services_title">"Our Core Services" Section Title</label>
            <input type="text" id="homepage_services_title" name="homepage_services_title" value="<?php echo htmlspecialchars($settings['homepage_services_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_products_title">"Complete Your Project" (Products) Section Title</label>
            <input type="text" id="homepage_products_title" name="homepage_products_title" value="<?php echo htmlspecialchars($settings['homepage_products_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_products_description">"Complete Your Project" (Products) Section Description</label>
            <textarea id="homepage_products_description" name="homepage_products_description" rows="3"><?php echo htmlspecialchars($settings['homepage_products_description']); ?></textarea>
        </div>
         <div class="form-group">
            <label for="homepage_why_choose_us_title">"Why Choose Us" Section Title</label>
            <input type="text" id="homepage_why_choose_us_title" name="homepage_why_choose_us_title" value="<?php echo htmlspecialchars($settings['homepage_why_choose_us_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_testimonials_title">"What Our Clients Say" Section Title</label>
            <input type="text" id="homepage_testimonials_title" name="homepage_testimonials_title" value="<?php echo htmlspecialchars($settings['homepage_testimonials_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_our_work_title">"Our Recent Work" Section Title</label>
            <input type="text" id="homepage_our_work_title" name="homepage_our_work_title" value="<?php echo htmlspecialchars($settings['homepage_our_work_title']); ?>">
        </div>
         <div class="form-group">
            <label for="homepage_our_work_description">"Our Recent Work" Section Description</label>
            <textarea id="homepage_our_work_description" name="homepage_our_work_description" rows="3"><?php echo htmlspecialchars($settings['homepage_our_work_description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="homepage_faq_title">"FAQ" Section Title</label>
            <input type="text" id="homepage_faq_title" name="homepage_faq_title" value="<?php echo htmlspecialchars($settings['homepage_faq_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_cta_title">"Join Our Community" (CTA) Section Title</label>
            <input type="text" id="homepage_cta_title" name="homepage_cta_title" value="<?php echo htmlspecialchars($settings['homepage_cta_title']); ?>">
        </div>
        <div class="form-group">
            <label for="homepage_cta_description">"Join Our Community" (CTA) Section Description</label>
            <textarea id="homepage_cta_description" name="homepage_cta_description" rows="3"><?php echo htmlspecialchars($settings['homepage_cta_description']); ?></textarea>
        </div>
    </div>

    <div class="content-card mt-4">
        <h3>Platform Information & Contact</h3>
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

    <div class="content-card mt-4">
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