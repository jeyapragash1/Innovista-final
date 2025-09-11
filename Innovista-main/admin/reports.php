<?php
// reports.php
require_once 'admin_header.php'; // session_start() and login check are handled here
require_once '../config/Database.php';

$db = new Database();
$conn = $db->getConnection();

// --- Income Report Data ---
$income_period = filter_input(INPUT_GET, 'income_period', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'monthly';
$income_data = [];
$income_query = "";

if ($income_period === 'monthly') {
    $income_query = "SELECT DATE_FORMAT(payment_date, '%Y-%m') as period, SUM(amount) as total_amount FROM payments GROUP BY period ORDER BY period DESC";
} elseif ($income_period === 'weekly') {
    $income_query = "SELECT YEARWEEK(payment_date) as period, SUM(amount) as total_amount FROM payments GROUP BY period ORDER BY period DESC";
} elseif ($income_period === 'yearly') {
    $income_query = "SELECT DATE_FORMAT(payment_date, '%Y') as period, SUM(amount) as total_amount FROM payments GROUP BY period ORDER BY period DESC";
}

$stmt_income = $conn->prepare($income_query);
$stmt_income->execute();
$income_data = $stmt_income->fetchAll(PDO::FETCH_ASSOC);

// --- Booking Report Data ---
$booking_period = filter_input(INPUT_GET, 'booking_period', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? 'monthly';
$booking_data = [];
$booking_query = "";

if ($booking_period === 'monthly') {
    $booking_query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total_bookings FROM quotations GROUP BY period ORDER BY period DESC";
} elseif ($booking_period === 'weekly') {
    $booking_query = "SELECT YEARWEEK(created_at) as period, COUNT(*) as total_bookings FROM quotations GROUP BY period ORDER BY period DESC";
} elseif ($booking_period === 'yearly') {
    $booking_query = "SELECT DATE_FORMAT(created_at, '%Y') as period, COUNT(*) as total_bookings FROM quotations GROUP BY period ORDER BY period DESC";
}

$stmt_booking = $conn->prepare($booking_query);
$stmt_booking->execute();
$booking_data = $stmt_booking->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Platform Reports</h2>

<div class="content-card">
    <div class="report-header">
        <h3>Income Report</h3>
        <div class="report-filters">
            <select name="income_period" onchange="window.location.href='reports.php?income_period='+this.value+'&booking_period=<?php echo htmlspecialchars($booking_period); ?>'">
                <option value="monthly" <?php echo ($income_period === 'monthly') ? 'selected' : ''; ?>>This Month</option>
                <option value="weekly" <?php echo ($income_period === 'weekly') ? 'selected' : ''; ?>>This Week</option>
                <option value="yearly" <?php echo ($income_period === 'yearly') ? 'selected' : ''; ?>>This Year</option>
            </select>
            <a href="generate_report.php?type=income&period=<?php echo htmlspecialchars($income_period); ?>" class="btn-download"><i class="fas fa-download"></i> Download PDF</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Total Income</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($income_data)): ?>
                    <?php foreach ($income_data as $data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['period']); ?></td>
                            <td>Rs <?php echo number_format($data['total_amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="text-align:center;">No income data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="content-card">
    <div class="report-header">
        <h3>Booking Report</h3>
         <div class="report-filters">
            <select name="booking_period" onchange="window.location.href='reports.php?booking_period='+this.value+'&income_period=<?php echo htmlspecialchars($income_period); ?>'">
                <option value="monthly" <?php echo ($booking_period === 'monthly') ? 'selected' : ''; ?>>This Month</option>
                <option value="weekly" <?php echo ($booking_period === 'weekly') ? 'selected' : ''; ?>>This Week</option>
                <option value="yearly" <?php echo ($booking_period === 'yearly') ? 'selected' : ''; ?>>This Year</option>
            </select>
            <a href="generate_report.php?type=booking&period=<?php echo htmlspecialchars($booking_period); ?>" class="btn-download"><i class="fas fa-download"></i> Download PDF</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Period</th>
                    <th>Total Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($booking_data)): ?>
                    <?php foreach ($booking_data as $data): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($data['period']); ?></td>
                            <td><?php echo number_format($data['total_bookings']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="text-align:center;">No booking data available.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>