<?php
require_once '../config/session.php'; 
protectPage('customer'); 

$pageTitle = 'Payment History';
require_once '../includes/user_dashboard_header.php';
// Database fetching logic for payments would go here
$payments = []; // In a real app, query your payments table
?>

<h2>My Payments</h2>
<p>View a complete history of all your transactions on the platform.</p>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Date</th><th>Project</th><th>Payment Type</th><th>Amount</th><th>Invoice</th></tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo htmlspecialchars($payment['date']); ?></td>
                    <td><?php echo htmlspecialchars($payment['project']); ?></td>
                    <td><?php echo htmlspecialchars($payment['type']); ?></td>
                    <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                    <td><a href="#" class="btn-view">Download</a></td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align: center;">You have not made any payments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>