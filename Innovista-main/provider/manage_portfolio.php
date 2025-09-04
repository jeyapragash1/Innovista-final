<?php 
$pageTitle = 'Manage Portfolio';
require_once 'provider_header.php'; 

require_once '../config/Database.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$provider_id = $_SESSION['user_id'] ?? 0;
$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT portfolio FROM service WHERE provider_id = :provider_id');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$portfolio = isset($row['portfolio']) ? ($row['portfolio'] ? explode(',', $row['portfolio']) : []) : [];
?>

<h2>Manage My Portfolio</h2>
<p>Showcase your best work to attract new clients. Upload high-quality images of completed projects.</p>

<div class="content-card">
    <h3>Upload New Photos</h3>
    <form action="upload_portfolio.php" method="POST" enctype="multipart/form-data" class="form-section">
        <div class="form-group">
            <label for="portfolio_photos">Select Images (you can select multiple)</label>
            <input type="file" id="portfolio_photos" name="portfolio_photos[]" multiple>
        </div>
        <button type="submit" class="btn-submit">Upload Photos</button>
    </form>
</div>

<div class="content-card">
    <h3>Current Gallery</h3>
    <div class="portfolio-gallery">
        <?php foreach($portfolio as $photo): ?>
            <div class="portfolio-image-card">
                <img src="../public/assets/images/<?php echo htmlspecialchars($photo); ?>" alt="Portfolio work">
                <button class="delete-photo-btn" title="Delete Photo"><i class="fas fa-trash"></i></button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'provider_footer.php'; ?>