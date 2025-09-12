<?php
// view_provider.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

$provider_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$provider_data = null;
$services_data = [];
$portfolio_items = [];

if (!$provider_id || !is_numeric($provider_id)) {
    header("Location: manage_providers.php?status=error&message=Invalid provider ID.");
    exit();
}

// Fetch provider's main user data
$stmt = $conn->prepare("SELECT id, name, email, role, status, provider_status, credentials_verified, phone, address, bio FROM users WHERE id = :id AND role = 'provider'");
$stmt->bindParam(':id', $provider_id, PDO::PARAM_INT);
$stmt->execute();
$provider_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$provider_data) {
    header("Location: manage_providers.php?status=error&message=Provider not found.");
    exit();
}

// Fetch provider's service details
$stmt_services = $conn->prepare("SELECT main_service, subcategories FROM service WHERE provider_id = :id");
$stmt_services->bindParam(':id', $provider_id, PDO::PARAM_INT);
$stmt_services->execute();
$services_data = $stmt_services->fetchAll(PDO::FETCH_ASSOC);

// Fetch provider's portfolio items
$stmt_portfolio = $conn->prepare("SELECT title, description, image_path FROM portfolio_items WHERE provider_id = :id ORDER BY created_at DESC");
$stmt_portfolio->bindParam(':id', $provider_id, PDO::PARAM_INT);
$stmt_portfolio->execute();
$portfolio_items = $stmt_portfolio->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Provider Details: <?php echo htmlspecialchars($provider_data['name']); ?></h2>

<div class="content-card">
    <h3>Basic Information</h3>
    <div class="form-grid">
        <div class="detail-item"><strong>Name:</strong> <?php echo htmlspecialchars($provider_data['name']); ?></div>
        <div class="detail-item"><strong>Email:</strong> <?php echo htmlspecialchars($provider_data['email']); ?></div>
        <div class="detail-item"><strong>Phone:</strong> <?php echo htmlspecialchars($provider_data['phone'] ?? 'N/A'); ?></div>
        <div class="detail-item"><strong>Address:</strong> <?php echo htmlspecialchars($provider_data['address'] ?? 'N/A'); ?></div>
        <div class="detail-item"><strong>Account Status:</strong> <span class="status-badge status-<?php echo strtolower($provider_data['status']); ?>"><?php echo htmlspecialchars($provider_data['status']); ?></span></div>
        <div class="detail-item"><strong>Approval Status:</strong> <span class="status-badge status-<?php echo strtolower($provider_data['provider_status']); ?>"><?php echo htmlspecialchars($provider_data['provider_status']); ?></span></div>
        <div class="detail-item"><strong>Credentials Verified:</strong> <span class="status-badge status-<?php echo strtolower($provider_data['credentials_verified'] == 'yes' ? 'verified' : 'pending'); ?>"><?php echo htmlspecialchars($provider_data['credentials_verified']); ?></span></div>
    </div>
    <div class="detail-item mt-3">
        <strong>Bio:</strong>
        <p><?php echo nl2br(htmlspecialchars($provider_data['bio'] ?? 'No bio provided.')); ?></p>
    </div>
</div>

<div class="content-card mt-4">
    <h3>Services Offered</h3>
    <?php if (!empty($services_data)): ?>
        <ul class="list-group">
            <?php foreach ($services_data as $service): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($service['main_service']); ?>:</strong>
                    <?php echo htmlspecialchars($service['subcategories']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No services registered by this provider.</p>
    <?php endif; ?>
</div>

<div class="content-card mt-4">
    <h3>Portfolio</h3>
    <?php if (!empty($portfolio_items)): ?>
        <div class="portfolio-grid">
            <?php foreach ($portfolio_items as $item): ?>
                <div class="portfolio-item">
                    <img src="../public/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($item['description'], 0, 100)) . (strlen($item['description']) > 100 ? '...' : ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No portfolio items uploaded by this provider.</p>
    <?php endif; ?>
</div>

<div class="action-buttons mt-4">
    <a href="manage_providers.php" class="btn-link">Back to Provider Approvals</a>
</div>

<?php require_once 'admin_footer.php'; ?>