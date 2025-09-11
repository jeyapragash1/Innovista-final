<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\admin\view_user.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc

$db = new Database();
$conn = $db->getConnection();

$user_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$user_data = null;
$service_data = null; // For providers

if (!$user_id || !is_numeric($user_id)) {
    header("Location: manage_users.php?status=error&message=Invalid user ID.");
    exit();
}

// Fetch user data
$stmt = $conn->prepare("
    SELECT id, name, email, role, status, provider_status, credentials_verified, phone, address, bio, profile_image_path, created_at
    FROM users
    WHERE id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
    header("Location: manage_users.php?status=error&message=User not found.");
    exit();
}

// If the user is a provider, fetch their service details
if ($user_data['role'] === 'provider') {
    $stmt_service = $conn->prepare("
        SELECT main_service, subcategories, portfolio AS provider_portfolio_text
        FROM service
        WHERE provider_id = :provider_id
    ");
    $stmt_service->bindParam(':provider_id', $user_id, PDO::PARAM_INT);
    $stmt_service->execute();
    $service_data = $stmt_service->fetch(PDO::FETCH_ASSOC);
}

// Optionally fetch recent activities for this user (similar to dashboard)
$user_activities = [];
try {
    $stmt_activities = $conn->prepare("
        (SELECT 'quotation_request' as type, q.id as entity_id, q.created_at, q.project_description as description
         FROM quotations q WHERE q.customer_id = :user_id OR q.provider_id = :user_id LIMIT 5)
        UNION ALL
        (SELECT 'payment' as type, p.id as entity_id, p.payment_date as created_at, CONCAT(p.payment_type, ' payment of Rs ', p.amount) as description
         FROM payments p JOIN custom_quotations cq ON p.quotation_id = cq.id WHERE cq.customer_id = :user_id OR cq.provider_id = :user_id LIMIT 5)
        UNION ALL
        (SELECT 'dispute' as type, d.id as entity_id, d.created_at, CONCAT('Dispute: ', d.reason) as description
         FROM disputes d WHERE d.reported_by_id = :user_id OR d.reported_against_id = :user_id LIMIT 5)
        UNION ALL
        (SELECT 'new_review' as type, r.id as entity_id, r.created_at, CONCAT('Left review for provider ', (SELECT name FROM users WHERE id=r.provider_id), ' (Rating: ', r.rating, ')') as description
         FROM reviews r WHERE r.customer_id = :user_id LIMIT 5)
        UNION ALL
        (SELECT 'received_review' as type, r.id as entity_id, r.created_at, CONCAT('Received review from customer ', (SELECT name FROM users WHERE id=r.customer_id), ' (Rating: ', r.rating, ')') as description
         FROM reviews r WHERE r.provider_id = :user_id LIMIT 5)
        ORDER BY created_at DESC LIMIT 10
    ");
    $stmt_activities->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_activities->execute();
    $user_activities = $stmt_activities->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error fetching user activities for view_user.php: " . $e->getMessage());
}


?>

<h2>User Details: <?php echo htmlspecialchars($user_data['name']); ?></h2>

<div class="content-card">
    <div class="profile-header-view text-center mb-4">
        <img src="<?php echo getImageSrc($user_data['profile_image_path'] ?? 'assets/images/default-avatar.jpg'); ?>" 
             alt="<?php echo htmlspecialchars($user_data['name']); ?> Profile" class="profile-avatar-lg mb-3">
        <h3><?php echo htmlspecialchars($user_data['name']); ?></h3>
        <p class="text-muted"><?php echo htmlspecialchars($user_data['email']); ?></p>
        <p>Registered On: <?php echo date('d M Y', strtotime($user_data['created_at'])); ?></p>
    </div>

    <h3>Account Information</h3>
    <div class="form-grid">
        <div class="detail-item"><strong>Role:</strong> <?php echo htmlspecialchars(ucfirst($user_data['role'])); ?></div>
        <div class="detail-item"><strong>Account Status:</strong> <span class="status-badge status-<?php echo strtolower($user_data['status']); ?>"><?php echo htmlspecialchars($user_data['status']); ?></span></div>
        <?php if ($user_data['role'] === 'provider'): ?>
            <div class="detail-item"><strong>Approval Status:</strong> <span class="status-badge status-<?php echo strtolower($user_data['provider_status']); ?>"><?php echo htmlspecialchars($user_data['provider_status']); ?></span></div>
            <div class="detail-item"><strong>Credentials Verified:</strong> <span class="status-badge status-<?php echo ($user_data['credentials_verified'] === 'yes') ? 'verified' : 'pending'; ?>"><?php echo htmlspecialchars($user_data['credentials_verified']); ?></span></div>
        <?php endif; ?>
        <div class="detail-item"><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone'] ?? 'N/A'); ?></div>
        <div class="detail-item"><strong>Address:</strong> <?php echo htmlspecialchars($user_data['address'] ?? 'N/A'); ?></div>
    </div>
    <div class="detail-item mt-3">
        <strong>Bio:</strong>
        <p><?php echo nl2br(htmlspecialchars($user_data['bio'] ?? 'No biography provided.')); ?></p>
    </div>
</div>

<?php if ($user_data['role'] === 'provider' && $service_data): ?>
<div class="content-card mt-4">
    <h3>Provider Service Details</h3>
    <div class="detail-item"><strong>Main Service(s):</strong> <?php echo htmlspecialchars($service_data['main_service'] ?? 'N/A'); ?></div>
    <div class="detail-item"><strong>Subcategories:</strong> <?php echo htmlspecialchars($service_data['subcategories'] ?? 'N/A'); ?></div>
    <div class="detail-item"><strong>Portfolio Link (Text):</strong> <?php echo htmlspecialchars($service_data['provider_portfolio_text'] ?? 'N/A'); ?></div>
</div>
<?php endif; ?>

<div class="content-card mt-4">
    <h3>Recent Activities for this User</h3>
    <ul class="activity-feed">
        <?php if (!empty($user_activities)): ?>
            <?php foreach ($user_activities as $activity): ?>
                <li class="activity-item">
                    <?php
                        $icon = '';
                        $icon_class = '';
                        $link = '#'; 
                        switch ($activity['type']) {
                            case 'quotation_request': $icon = 'fas fa-file-invoice-dollar'; $icon_class = 'blue'; $link = 'view_quotation.php?id=' . htmlspecialchars($activity['entity_id']); break;
                            case 'payment': $icon = 'fas fa-dollar-sign'; $icon_class = 'green'; break; // Link to a payment detail page
                            case 'dispute': $icon = 'fas fa-gavel'; $icon_class = 'red'; $link = 'view_dispute.php?id=' . htmlspecialchars($activity['entity_id']); break;
                            case 'new_review': $icon = 'fas fa-star'; $icon_class = 'yellow'; break; // Link to review detail
                            case 'received_review': $icon = 'fas fa-star'; $icon_class = 'yellow'; break;
                            default: $icon = 'fas fa-info-circle'; $icon_class = 'grey'; break;
                        }
                    ?>
                    <div class="activity-icon <?php echo $icon_class; ?>"><i class="<?php echo $icon; ?>"></i></div>
                    <div class="activity-content">
                        <p class="activity-text">
                            <a href="<?php echo htmlspecialchars($link); ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $activity['type'])) . ": " . htmlspecialchars(substr($activity['description'], 0, 100)) . (strlen($activity['description']) > 100 ? '...' : ''); ?>
                            </a>
                        </p>
                        <span class="activity-time"><?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No recent activity found for this user.</p>
        <?php endif; ?>
    </ul>
</div>


<div class="action-buttons mt-4">
    <a href="edit_user.php?id=<?php echo htmlspecialchars($user_data['id']); ?>" class="btn-submit">Edit User</a>
    <a href="manage_users.php" class="btn-link">Back to User Management</a>
</div>

<?php require_once 'admin_footer.php'; ?>

<!-- Add basic styling to admin.css if not already present -->
<style>
    .profile-header-view {
        text-align: center;
        margin-bottom: 2rem;
    }
    .profile-avatar-lg {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid var(--primary-color, #0d9488);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
</style>