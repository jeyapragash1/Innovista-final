<?php
// view_quotation.php
require_once 'admin_header.php'; // Ensures admin is logged in
require_once '../config/Database.php';
require_once '../public/session.php'; // For getImageSrc if needed for photos

$db = new Database();
$conn = $db->getConnection();

$quotation_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$quotation_data = null;

if (!$quotation_id || !is_numeric($quotation_id)) {
    header("Location: manage_quotations.php?status=error&message=Invalid quotation ID.");
    exit();
}

// Fetch quotation details from both 'quotations' and 'custom_quotations'
$stmt = $conn->prepare("
    SELECT q.id AS request_id, q.service_type, q.project_description AS request_project_description, q.status AS request_status, q.created_at AS request_created_at, q.photos AS request_photos,
           c.id AS customer_id, c.name AS customer_name, c.email AS customer_email,
           p.id AS provider_id, p.name AS provider_name, p.email AS provider_email,
           cq.id AS custom_quote_id, cq.quotation_id AS cq_quotation_id_ref, cq.amount AS quoted_amount, cq.advance, cq.start_date, cq.end_date,
           cq.validity, cq.provider_notes, cq.photos AS custom_quote_photos, cq.status AS custom_quote_status, cq.created_at AS custom_quote_created_at,
           cq.project_description AS custom_quote_project_description
    FROM quotations q
    JOIN users c ON q.customer_id = c.id
    JOIN users p ON q.provider_id = p.id
    LEFT JOIN custom_quotations cq ON q.id = cq.quotation_id
    WHERE q.id = :id
");
$stmt->bindParam(':id', $quotation_id, PDO::PARAM_INT);
$stmt->execute();
$quotation_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quotation_data) {
    header("Location: manage_quotations.php?status=error&message=Quotation not found.");
    exit();
}

// Check for associated project
$project_data = null;
if ($quotation_data['custom_quote_id']) { // Assuming project is linked to custom_quotations ID
    $stmt_project = $conn->prepare("SELECT id, status, start_date, end_date FROM projects WHERE quotation_id = :custom_quote_id");
    $stmt_project->bindParam(':custom_quote_id', $quotation_data['custom_quote_id'], PDO::PARAM_INT);
    $stmt_project->execute();
    $project_data = $stmt_project->fetch(PDO::FETCH_ASSOC);
}

// Check for payments
$payments_data = [];
if ($quotation_data['custom_quote_id']) {
    $stmt_payments = $conn->prepare("SELECT id, amount, payment_type, transaction_id, payment_date FROM payments WHERE quotation_id = :custom_quote_id ORDER BY payment_date DESC");
    $stmt_payments->bindParam(':custom_quote_id', $quotation_data['custom_quote_id'], PDO::PARAM_INT);
    $stmt_payments->execute();
    $payments_data = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Quotation Details: #INV-<?php echo str_pad($quotation_data['request_id'], 4, '0', STR_PAD_LEFT); ?></h2>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <h3>Request Information</h3>
    <div class="form-grid">
        <div class="detail-item"><strong>Customer:</strong> <a href="manage_users.php?id=<?php echo $quotation_data['customer_id']; ?>"><?php echo htmlspecialchars($quotation_data['customer_name']); ?></a> (<?php echo htmlspecialchars($quotation_data['customer_email']); ?>)</div>
        <div class="detail-item"><strong>Provider:</strong> <a href="manage_providers.php?id=<?php echo $quotation_data['provider_id']; ?>"><?php echo htmlspecialchars($quotation_data['provider_name']); ?></a> (<?php echo htmlspecialchars($quotation_data['provider_email']); ?>)</div>
        <div class="detail-item"><strong>Service Type:</strong> <?php echo htmlspecialchars($quotation_data['service_type']); ?></div>
        <div class="detail-item"><strong>Request Status:</strong> <span class="status-badge status-<?php echo strtolower($quotation_data['request_status'] == 'approved' ? 'approved' : ($quotation_data['request_status'] == 'awaiting quote' ? 'pending' : 'rejected')); ?>"><?php echo htmlspecialchars($quotation_data['request_status']); ?></span></div>
        <div class="detail-item"><strong>Request Date:</strong> <?php echo date('d M Y, H:i', strtotime($quotation_data['request_created_at'])); ?></div>
    </div>
    <div class="detail-item mt-3">
        <strong>Project Description (Customer Request):</strong>
        <p><?php echo nl2br(htmlspecialchars($quotation_data['request_project_description'])); ?></p>
    </div>
    <?php if (!empty($quotation_data['request_photos'])): ?>
        <div class="detail-item mt-3">
            <strong>Customer Provided Photos:</strong>
            <!-- You'll need to handle displaying these, assuming they are comma-separated paths or JSON -->
            <p> (Image display logic here) </p>
        </div>
    <?php endif; ?>
</div>

<?php if ($quotation_data['custom_quote_id']): ?>
<div class="content-card mt-4">
    <h3>Provider's Custom Quotation</h3>
    <div class="form-grid">
        <div class="detail-item"><strong>Quoted Amount:</strong> Rs <?php echo number_format($quotation_data['quoted_amount'], 2); ?></div>
        <div class="detail-item"><strong>Advance Payment:</strong> Rs <?php echo number_format($quotation_data['advance'], 2); ?></div>
        <div class="detail-item"><strong>Validity:</strong> <?php echo htmlspecialchars($quotation_data['validity']); ?> days</div>
        <div class="detail-item"><strong>Start Date:</strong> <?php echo htmlspecialchars($quotation_data['start_date']); ?></div>
        <div class="detail-item"><strong>End Date:</strong> <?php echo htmlspecialchars($quotation_data['end_date']); ?></div>
        <div class="detail-item"><strong>Quote Status:</strong> <span class="status-badge status-<?php echo strtolower($quotation_data['custom_quote_status'] == 'approved' ? 'approved' : ($quotation_data['custom_quote_status'] == 'pending' ? 'pending' : 'rejected')); ?>"><?php echo htmlspecialchars($quotation_data['custom_quote_status']); ?></span></div>
        <div class="detail-item"><strong>Quote Date:</strong> <?php echo date('d M Y, H:i', strtotime($quotation_data['custom_quote_created_at'])); ?></div>
    </div>
    <div class="detail-item mt-3">
        <strong>Project Description (Provider Quote):</strong>
        <p><?php echo nl2br(htmlspecialchars($quotation_data['custom_quote_project_description'] ?? 'No specific description provided by provider.')); ?></p>
    </div>
    <div class="detail-item mt-3">
        <strong>Provider Notes:</strong>
        <p><?php echo nl2br(htmlspecialchars($quotation_data['provider_notes'] ?? 'No notes.')); ?></p>
    </div>
    <?php if (!empty($quotation_data['custom_quote_photos'])): ?>
        <div class="detail-item mt-3">
            <strong>Provider Provided Photos:</strong>
            <!-- You'll need to handle displaying these, assuming they are comma-separated paths or JSON -->
            <p> (Image display logic here) </p>
        </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="content-card mt-4">
    <p>No custom quotation has been submitted by the provider for this request yet.</p>
</div>
<?php endif; ?>

<?php if ($project_data): ?>
<div class="content-card mt-4">
    <h3>Associated Project</h3>
    <div class="detail-item"><strong>Project ID:</strong> <?php echo htmlspecialchars($project_data['id']); ?></div>
    <div class="detail-item"><strong>Project Status:</strong> <span class="status-badge status-<?php echo strtolower($project_data['status']); ?>"><?php echo htmlspecialchars($project_data['status']); ?></span></div>
    <div class="detail-item"><strong>Project Start:</strong> <?php echo htmlspecialchars($project_data['start_date'] ?? 'N/A'); ?></div>
    <div class="detail-item"><strong>Project End:</strong> <?php echo htmlspecialchars($project_data['end_date'] ?? 'N/A'); ?></div>
    <!-- Add link to view project updates if you have a page for that -->
</div>
<?php endif; ?>

<?php if (!empty($payments_data)): ?>
<div class="content-card mt-4">
    <h3>Payments Made</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Type</th>
                <th>Transaction ID</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments_data as $payment): ?>
            <tr>
                <td><?php echo htmlspecialchars($payment['id']); ?></td>
                <td>Rs <?php echo number_format($payment['amount'], 2); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($payment['payment_type'])); ?></td>
                <td><?php echo htmlspecialchars($payment['transaction_id']); ?></td>
                <td><?php echo date('d M Y, H:i', strtotime($payment['payment_date'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="action-buttons mt-4">
    <a href="manage_quotations.php" class="btn-link">Back to Quotations</a>
</div>

<?php require_once 'admin_footer.php'; ?>