<?php 
require_once 'admin_header.php'; 
require_once '../config/Database.php';

session_start();

// DB connection
$db = (new Database())->getConnection();

// Fetch all quotations with customer + provider names
$query = "
    SELECT q.id, q.project_description, q.status, q.price, q.created_at,
           c.name AS customer_name, 
           p.name AS provider_name
    FROM quotations q
    JOIN users c ON q.customer_id = c.id
    JOIN users p ON q.provider_id = p.id
    ORDER BY q.created_at DESC
";
$stmt = $db->prepare($query);
$stmt->execute();
$quotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Quotations</h2>
<p>Review all quotations submitted on the platform.</p>

<div class="content-card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Quote ID</th>
                    <th>Customer</th>
                    <th>Provider</th>
                    <th>Project Summary</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($quotations)): ?>
                    <?php foreach ($quotations as $quote): ?>
                        <tr>
                            <td>#INV-<?php echo str_pad($quote['id'], 4, '0', STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($quote['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($quote['provider_name']); ?></td>
                            <td><?php echo htmlspecialchars($quote['project_description']); ?></td>
                            <td>Rs <?php echo number_format($quote['price'], 2); ?></td>
                            <td>
                                <span class="status-badge 
                                    <?php 
                                        echo strtolower($quote['status']) == 'approved' ? 'status-approved' : 
                                             (strtolower($quote['status']) == 'awaiting quote' ? 'status-pending' : 'status-rejected'); 
                                    ?>">
                                    <?php echo htmlspecialchars($quote['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d M Y', strtotime($quote['created_at'])); ?></td>
                            <td><a href="view_quotation.php?id=<?php echo $quote['id']; ?>" class="btn-link">View Details</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center;">No quotations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
