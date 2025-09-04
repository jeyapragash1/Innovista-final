<?php require_once 'admin_header.php'; ?>

<h2>Platform Reports</h2>

<div class="content-card">
    <div class="report-header">
        <h3>Income Report</h3>
        <div class="report-filters">
            <select name="income_period">
                <option value="monthly">This Month</option>
                <option value="weekly">This Week</option>
                <option value="yearly">This Year</option>
            </select>
            <a href="#" class="btn-download"><i class="fas fa-download"></i> Download</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
            <!-- Income Report Table Here -->
        </table>
    </div>
</div>

<div class="content-card">
    <div class="report-header">
        <h3>Booking Report</h3>
         <div class="report-filters">
            <select name="booking_period">
                <option value="monthly">This Month</option>
                <option value="weekly">This Week</option>
                <option value="yearly">This Year</option>
            </select>
            <a href="#" class="btn-download"><i class="fas fa-download"></i> Download</a>
        </div>
    </div>
    <div class="table-wrapper">
        <table>
             <!-- Booking Report Table Here -->
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>