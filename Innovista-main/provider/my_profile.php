<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'My Profile';
require_once '../provider/provider_header.php'; 

require_once '../config/Database.php';

// Add CSS file
echo '<link rel="stylesheet" href="../public/assets/css/provider-profile.css">';
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

<div class="profile-container">
    <h1 class="page-title">Manage My Business Profile</h1>
    <p class="page-subtitle">Keep your information up to date to attract more clients and grow your business</p>

    <!-- Business Stats -->
    <div class="profile-stats">
        <div class="stat-card">
            <div class="stat-number">15</div>
            <div class="stat-label">Total Projects</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">4.8</div>
            <div class="stat-label">Average Rating</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">95%</div>
            <div class="stat-label">Response Rate</div>
        </div>
    </div>

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
                <textarea id="bio" name="bio" rows="4" placeholder="Tell potential clients about your business, experience, and what makes you unique..."><?php echo htmlspecialchars($provider['provider_address'] ?? ''); ?></textarea>
            </div>
            <button type="submit" name="update_details" class="btn-submit">💼 Save Business Info</button>
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
        <p style="margin-top: 1rem; color: var(--text-light); font-size: 0.9rem;">
            <i class="fas fa-info-circle"></i> To add or remove services, please contact support.
        </p>
    </div>

g    <div class="content-card">
        <h3>Portfolio Showcase</h3>
        <form action="upload_portfolio.php" method="POST" enctype="multipart/form-data" class="form-section">
            <div class="form-group">
                <label for="portfolio_photos">
                    <i class="fas fa-cloud-upload-alt"></i> Upload New Photos
                </label>
                <input type="file" id="portfolio_photos" name="portfolio_photos[]" multiple accept="image/*">
                <p class="upload-hint">Drag and drop your photos here or click to select files</p>
            </div>
            <button type="submit" class="btn-submit">
                <i class="fas fa-upload"></i> Upload Photos
            </button>
        </form>
        <div class="portfolio-gallery">
            <?php 
            $portfolio = isset($provider['portfolio']) ? (is_array($provider['portfolio']) ? $provider['portfolio'] : explode(',', $provider['portfolio'])) : [];
            foreach($portfolio as $photo): ?>
                <div class="portfolio-image-card">
                    <img src="../assets/images/<?php echo htmlspecialchars($photo); ?>" alt="Portfolio work">
                    <button class="delete-photo-btn" title="Delete Photo">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>