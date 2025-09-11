<?php
// view_dispute.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

$dispute_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$dispute_data = null;

if (!$dispute_id || !is_numeric($dispute_id)) {
    header("Location: resolve_disputes.php?status=error&message=Invalid dispute ID.");
    exit();
}

// Fetch dispute data with customer and provider names
$stmt = $conn->prepare("
    SELECT d.id, d.quotation_id, d.reason, d.status, d.admin_notes, d.created_at, d.updated_at,
           c.id AS customer_id, c.name AS customer_name,
           p.id AS provider_id, p.name AS provider_name
    FROM disputes d
    JOIN users c ON d.reported_by_id = c.id
    JOIN users p ON d.reported_against_id = p.id
    WHERE d.id = :id
");
$stmt->bindParam(':id', $dispute_id, PDO::PARAM_INT);
$stmt->execute();
$dispute_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dispute_data) {
    header("Location: resolve_disputes.php?status=error&message=Dispute not found.");
    exit();
}

// Fetch associated quotation details if available
$quotation_details = null;
if ($dispute_data['quotation_id']) {
    // Assuming quotation_id in disputes links to custom_quotations.id
    $stmt_quote = $conn->prepare("
        SELECT cq.id AS custom_quotation_id, cq.quotation_id AS original_quotation_id, 
               cq.project_description, cq.amount 
        FROM custom_quotations cq 
        WHERE cq.id = :quote_id
    ");
    $stmt_quote->bindParam(':quote_id', $dispute_data['quotation_id'], PDO::PARAM_INT);
    $stmt_quote->execute();
    $quotation_details = $stmt_quote->fetch(PDO::FETCH_ASSOC);
}
?>

<h2>Dispute Details: #D-<?php echo str_pad($dispute_data['id'], 3, '0', STR_PAD_LEFT); ?></h2>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <h3>Dispute Overview</h3>
    <div class="detail-item"><strong>Reported By:</strong> <a href="view_user.php?id=<?php echo htmlspecialchars($dispute_data['customer_id']); ?>"><?php echo htmlspecialchars($dispute_data['customer_name']); ?></a></div>
    <div class="detail-item"><strong>Reported Against:</strong> <a href="view_provider.php?id=<?php echo htmlspecialchars($dispute_data['provider_id']); ?>"><?php echo htmlspecialchars($dispute_data['provider_name']); ?></a></div>
    <div class="detail-item"><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower($dispute_data['status'] === 'resolved' ? 'approved' : 'pending'); ?>"><?php echo htmlspecialchars($dispute_data['status']); ?></span></div>
    <div class="detail-item"><strong>Reported On:</strong> <?php echo date('d M Y, H:i', strtotime($dispute_data['created_at'])); ?></div>
    <div class="detail-item"><strong>Last Updated:</strong> <?php echo date('d M Y, H:i', strtotime($dispute_data['updated_at'])); ?></div>
    
    <div class="detail-item mt-3">
        <strong>Reason for Dispute:</strong>
        <p><?php echo nl2br(htmlspecialchars($dispute_data['reason'])); ?></p>
    </div>

    <?php if ($quotation_details): ?>
    <div class="detail-item mt-3">
        <strong>Associated Quotation (Request ID: #INV-<?php echo str_pad($quotation_details['original_quotation_id'], 4, '0', STR_PAD_LEFT); ?>):</strong>
        <p>Project Description: <?php echo htmlspecialchars($quotation_details['project_description']); ?></p>
        <p>Quoted Amount: Rs <?php echo number_format($quotation_details['amount'], 2); ?></p>
        <a href="view_quotation.php?id=<?php echo htmlspecialchars($quotation_details['original_quotation_id']); ?>" class="btn-link small">View Full Quotation</a>
    </div>
    <?php endif; ?>
</div>

<div class="content-card mt-4">
    <h3>Admin Resolution</h3>
    <form action="update_dispute.php?id=<?php echo htmlspecialchars($dispute_data['id']); ?>" method="POST">
        <div class="form-group">
            <label for="status_update">Update Status:</label>
            <select id="status_update" name="status" <?php echo ($dispute_data['status'] === 'resolved') ? 'disabled' : ''; ?>>
                <option value="open" <?php echo ($dispute_data['status'] === 'open') ? 'selected' : ''; ?>>Open</option>
                <option value="under_review" <?php echo ($dispute_data['status'] === 'under_review') ? 'selected' : ''; ?>>Under Review</option>
                <option value="resolved" <?php echo ($dispute_data['status'] === 'resolved') ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </div>
        <div class="form-group">
            <label for="admin_notes">Admin Notes:</label>
            <textarea id="admin_notes" name="admin_notes" rows="6" <?php echo ($dispute_data['status'] === 'resolved') ? 'disabled' : ''; ?>><?php echo htmlspecialchars($dispute_data['admin_notes'] ?? ''); ?></textarea>
        </div>
        <?php if ($dispute_data['status'] !== 'resolved'): ?>
            <button type="submit" class="btn-submit">Save Resolution</button>
        <?php else: ?>
            <p class="text-info">This dispute is already resolved.</p>
        <?php endif; ?>
        <a href="resolve_disputes.php" class="btn-link">Back to Disputes</a>
    </form>
</div>

<?php require_once 'admin_footer.php'; ?>