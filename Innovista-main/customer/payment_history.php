<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\payment_history.php

require_once '../public/session.php'; 
require_once '../handlers/flash_message.php'; // For display_flash_message

// --- User-specific authentication function ---
if (!function_exists('protectPage')) {
    function protectPage(string $requiredRole): void {
        if (!isUserLoggedIn()) {
            header("Location: ../public/login.php");
            exit();
        }
        if (getUserRole() !== $requiredRole && getUserRole() !== 'admin') { 
            set_flash_message('error', 'Access denied. You do not have permission to view this page.');
            header("Location: ../public/index.php");
            exit();
        }
    }
}
protectPage('customer'); 

$pageTitle = 'Payment History';
require_once '../includes/user_dashboard_header.php';
require_once '../config/Database.php';

$db = (new Database())->getConnection();
$loggedInUserId = getUserId(); // Get the logged-in customer's ID

$payments = []; // Initialize payments array

// Fetch payments made by the logged-in customer
try {
    $stmt_payments = $db->prepare("
        SELECT 
            py.id AS payment_id,
            py.amount,
            py.payment_type,
            py.transaction_id,
            py.payment_date,
            cq.project_description,
            prov.name AS provider_name
        FROM payments py
        JOIN custom_quotations cq ON py.quotation_id = cq.id
        JOIN users prov ON cq.provider_id = prov.id
        WHERE cq.customer_id = :customer_id
        ORDER BY py.payment_date DESC
    ");
    $stmt_payments->bindParam(':customer_id', $loggedInUserId, PDO::PARAM_INT);
    $stmt_payments->execute();
    $payments = $stmt_payments->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Payment history page error: " . $e->getMessage());
    set_flash_message('error', 'Error loading payment history. Please try again.');
}

?>

<?php display_flash_message(); ?>

<h2>My Payments</h2>
<p>View a complete history of all your transactions on the platform.</p>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th>Provider</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): foreach ($payments as $payment): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($payment['payment_date'])); ?></td>
                    <td><?php echo htmlspecialchars($payment['project_description']); ?></td>
                    <td><?php echo htmlspecialchars($payment['provider_name']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($payment['payment_type'])); ?></td>
                    <td>Rs <?php echo number_format($payment['amount'], 2); ?></td>
                    <td>
                        <a href="../handlers/generate_invoice.php?payment_id=<?php echo htmlspecialchars($payment['payment_id']); ?>" class="btn-view" target="_blank">Download</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align: center;">You have not made any payments yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>