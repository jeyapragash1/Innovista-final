<?php 
// manage_quotations.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

// DB connection
$db = (new Database())->getConnection();

// Fetch all quotations with customer + provider names, and the amount/status from custom_quotations
$query = "
    SELECT q.id, q.project_description, q.service_type, q.created_at,
           c.name AS customer_name, 
           p.name AS provider_name,
           cq.amount AS price,     -- Get amount from custom_quotations
           cq.status AS custom_quotation_status, -- Get status from custom_quotations
           q.status AS customer_request_status -- Status of the initial customer request
    FROM quotations q
    JOIN users c ON q.customer_id = c.id
    JOIN users p ON q.provider_id = p.id
    LEFT JOIN custom_quotations cq ON q.id = cq.quotation_id -- Use LEFT JOIN as a custom quote might not exist yet for a request
    ORDER BY q.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute();
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Quotations</h2>
<p>Review all quotations submitted on the platform.</p>

<?php
if (isset($_GET['status']) && isset($_GET['message'])) {
    $status_class = ($_GET['status'] === 'success') ? 'success' : 'error';
    echo "<div class='alert alert-{$status_class}'>" . htmlspecialchars($_GET['message']) . "</div>";
}
?>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <!-- <th>Quote ID</th> -->
                    <th>Customer</th>
                    <th>Provider</th>
                    <th>Service Type</th>
                    <th>Project Summary</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($quotations)): ?>
                    <?php foreach ($quotations as $quote):
                        // Determine which status to display. If a custom quote exists, use its status.
                        // Otherwise, use the status of the initial customer request.
                        $display_status = $quote['custom_quotation_status'] ?? $quote['customer_request_status'];
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($quote['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($quote['provider_name']); ?></td>
                            <td><?php echo htmlspecialchars($quote['service_type']); ?></td>
                            <td><?php echo htmlspecialchars(substr($quote['project_description'], 0, 50)) . (strlen($quote['project_description']) > 50 ? '...' : ''); ?></td>
                            <td><?php echo ($quote['price'] !== null) ? 'Rs ' . number_format($quote['price'], 2) : 'N/A'; ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php
                                        // Map various statuses to CSS classes
                                        $status_lower = strtolower($display_status);
                                        if (str_contains($status_lower, 'approved') || str_contains($status_lower, 'sent')) {
                                            echo 'status-approved';
                                        } elseif (str_contains($status_lower, 'pending') || str_contains($status_lower, 'awaiting')) {
                                            echo 'status-pending';
                                        } elseif (str_contains($status_lower, 'declined') || str_contains($status_lower, 'rejected')) {
                                            echo 'status-rejected';
                                        } else {
                                            echo 'status-info'; // Default or unknown status
                                        }
                                    ?>">
                                    <?php echo htmlspecialchars($display_status); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($quote['created_at'])); ?></td>
                            <td class="action-buttons">
                                <a href="view_quotation.php?id=<?php echo $quote['id']; ?>" class="btn-link">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">No quotations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>