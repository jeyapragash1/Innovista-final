<?php require_once 'admin_header.php'; ?>

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
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#INV-0078</td>
                    <td>Alice Johnson</td>
                    <td>Modern Living</td>
                    <td>$7,500.00</td>
                    <td><span class="status-badge status-approved">Approved</span></td>
                    <td><a href="#" class="btn-link">View Details</a></td>
                </tr>
                <tr>
                    <td>#INV-0077</td>
                    <td>Bob Williams</td>
                    <td>Elegant Interiors</td>
                    <td>$1,200.00</td>
                    <td><span class="status-badge status-pending">Pending</span></td>
                    <td><a href="#" class="btn-link">View Details</a></td>
                </tr>
                 <tr>
                    <td>#INV-0076</td>
                    <td>Eve Davis</td>
                    <td>Classic Restorations Inc.</td>
                    <td>$2,200.00</td>
                    <td><span class="status-badge status-rejected">Declined</span></td>
                    <td><a href="#" class="btn-link">View Details</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>