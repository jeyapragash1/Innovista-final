<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'My Profile';
require_once '../provider/provider_header.php'; 

require_once '../config/Database.php';
$provider_id = (isset($_SESSION) && isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0;
$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT * FROM service WHERE provider_id = :provider_id LIMIT 1');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$provider = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$provider) {
    $provider = [
        'provider_name' => '',
        'provider_email' => '',
        'provider_phone' => '',
        'provider_address' => '',
        'main_service' => '',
        'subcategories' => '',
        'provider_cv' => '',
        'portfolio' => []
    ];
}
// ...existing code...
?>

<h2>Manage My Business Profile</h2>
<p>Keep your information up to date to attract more clients.</p>

<div class="content-card">
    <h3>Business Information</h3>
    <form action="update_profile.php" method="POST" class="form-section">
        <div class="form-grid">
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($provider['provider_name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Contact Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($provider['provider_email'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label for="phone">Contact Phone</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($provider['provider_phone'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label for="bio">Company Bio / Description</label>
            <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($provider['provider_address'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="update_details" class="btn-submit">Save Business Info</button>
    </form>
</div>

<div class="content-card">
    <h3>My Services</h3>
    <div class="service-tags">
        <?php 
        $services = isset($provider['main_service']) ? explode(',', $provider['main_service']) : [];
        foreach($services as $service): ?>
            <span class="service-tag"><?php echo htmlspecialchars($service); ?></span>
        <?php endforeach; ?>
    </div>
    <p style="margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">To add or remove services, please contact support.</p>
</div>

<div class="content-card">
    <h3>Manage Portfolio</h3>
    <form action="upload_portfolio.php" method="POST" enctype="multipart/form-data" class="form-section">
        <div class="form-group">
            <label for="portfolio_photos">Upload New Photos</label>
            <input type="file" id="portfolio_photos" name="portfolio_photos[]" multiple>
        </div>
        <button type="submit" class="btn-submit">Upload Photos</button>
    </form>
    <div class="portfolio-gallery">
        <?php 
        $portfolio = isset($provider['portfolio']) ? (is_array($provider['portfolio']) ? $provider['portfolio'] : explode(',', $provider['portfolio'])) : [];
        foreach($portfolio as $photo): ?>
            <div class="portfolio-image-card">
                <img src="../assets/images/<?php echo htmlspecialchars($photo); ?>" alt="Portfolio work">
                <button class="delete-photo-btn" title="Delete Photo"><i class="fas fa-trash"></i></button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>