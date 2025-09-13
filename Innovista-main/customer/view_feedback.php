<?php
require_once __DIR__ . '/../config/session.php';
protectPage('customer');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/Review.php';

$customer_id = $_SESSION['user_id'];
$booking_id = (int)($_GET['booking_id'] ?? 0);

$database = new Database();
$db = $database->getConnection();
$reviewObj = new Review($db);

if ($booking_id <= 0) {
    // show error or redirect
    header('Location: my_projects.php?msg=Invalid+booking');
    exit;
}

// find quotation_id for booking
$stmt = $db->prepare("SELECT q.id AS quotation_id FROM projects p JOIN quotations q ON p.quotation_id = q.id WHERE p.id = ? AND q.customer_id = ? LIMIT 1");
$stmt->execute([$booking_id, $customer_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    header('Location: my_projects.php?msg=Invalid+booking');
    exit;
}

$quotationId = (int)$row['quotation_id'];
$feedback = $reviewObj->getFeedbackByBooking($quotationId, $customer_id);

// include header/footer as your layout
require_once __DIR__ . '/../includes/user_dashboard_header.php';
?>

<h2>My Feedback</h2>
<div class="dashboard-section">
  <div class="content-card">
    <?php if ($feedback): ?>
      <table class="feedback-table">
        <tr><th>Rating</th><td>
          <?php for($i=1;$i<=5;$i++): ?>
            <?= $i <= (int)$feedback['rating'] ? "<span style='color:#f7c600'>★</span>" : "<span style='color:#ccc'>★</span>" ?>
          <?php endfor; ?>
        </td></tr>
        <tr><th>Comment</th><td><?= htmlspecialchars($feedback['comment']); ?></td></tr>
        <tr><th>Date</th><td><?= $feedback['created_at']; ?></td></tr>
      </table>
    <?php else: ?>
      <div class="alert">No feedback found.</div>
    <?php endif; ?>

    <a href="my_projects.php" class="btn-view">← Back to Projects</a>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/user_dashboard_footer.php'; ?>

