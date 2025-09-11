<?php 
// resolve_disputes.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

// DB connection
$db = (new Database())->getConnection();

// Fetch all disputes with customer + provider names
$query = "
    SELECT d.id, d.reason, d.status, d.created_at,
           c.name AS customer_name,
           p.name AS provider_name,
           q.id AS quotation_table_id -- To link back to the original quotation request if needed
    FROM disputes d
    JOIN users c ON d.reported_by_id = c.id
    JOIN users p ON d.reported_against_id = p.id
    LEFT JOIN custom_quotations cq ON d.quotation_id = cq.id -- If quotation_id in disputes references custom_quotations
    LEFT JOIN quotations q ON cq.quotation_id = q.id -- If custom_quotations links to quotations
    ORDER BY d.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute();
$disputes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate into open and resolved
$openDisputes = array_filter($disputes, fn($d) => strtolower($d['status']) !== 'resolved');
$resolvedDisputes = array_filter($disputes, fn($d) => strtolower($d['status']) === 'resolved');
?>

<h2>Resolve Disputes</h2>
<p>Review and resolve issues reported between users to maintain platform integrity.</p>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <h3>Open Disputes</h3>
    <ul class="disputes-list">
        <?php if (!empty($openDisputes)): ?>
            <?php foreach ($openDisputes as $dispute): ?>
            <li class="dispute-item">
                <div class="dispute-icon"><i class="fas fa-exclamation-circle" style="color: #e74c3c;"></i></div>
                <div class="dispute-details">
                    <p>Dispute #D-<?php echo str_pad($dispute['id'], 3, '0', STR_PAD_LEFT); ?>: <?php echo htmlspecialchars($dispute['reason']); ?></p>
                    <span>Customer: <strong><?php echo htmlspecialchars($dispute['customer_name']); ?></strong> vs. Provider: <strong><?php echo htmlspecialchars($dispute['provider_name']); ?></strong></span>
                </div>
                <div class="dispute-status">
                    <span class="status-badge status-pending"><?php echo htmlspecialchars($dispute['status']); ?></span>
                </div>
                <div class="action-buttons">
                    <a href="view_dispute.php?id=<?php echo $dispute['id']; ?>" class="btn-link">View Details</a>
                </div>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li style="text-align:center;">No open disputes.</li>
        <?php endif; ?>
    </ul>
</div>

<div class="content-card">
    <h3>Resolved Disputes</h3>
    <ul class="disputes-list">
        <?php if (!empty($resolvedDisputes)): ?>
            <?php foreach ($resolvedDisputes as $dispute): ?>
            <li class="dispute-item">
                <div class="dispute-icon"><i class="fas fa-check-circle" style="color: #27ae60;"></i></div>
                <div class="dispute-details">
                    <p>Dispute #D-<?php echo str_pad($dispute['id'], 3, '0', STR_PAD_LEFT); ?>: <?php echo htmlspecialchars($dispute['reason']); ?></p>
                    <span>Customer: <strong><?php echo htmlspecialchars($dispute['customer_name']); ?></strong> vs. Provider: <strong><?php echo htmlspecialchars($dispute['provider_name']); ?></strong></span>
                </div>
                <div class="dispute-status">
                    <span class="status-badge status-approved"><?php echo htmlspecialchars($dispute['status']); ?></span>
                </div>
                <div class="action-buttons">
                    <a href="view_dispute.php?id=<?php echo $dispute['id']; ?>" class="btn-link">View Details</a>
                </div>
            </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li style="text-align:center;">No resolved disputes.</li>
        <?php endif; ?>
    </ul>
</div>

<?php require_once 'admin_footer.php'; ?>