<?php require_once 'admin_header.php'; ?>

<h2>Resolve Disputes</h2>
<p>Review and resolve issues reported between users to maintain platform integrity.</p>

<div class="content-card">
    <h3>Open Disputes</h3>
    <ul class="disputes-list">
        <li class="dispute-item">
            <div class="dispute-icon"><i class="fas fa-exclamation-circle" style="color: #e74c3c;"></i></div>
            <div class="dispute-details">
                <p>Dispute #D-001: Project Completion Delay</p>
                <span>Customer: <strong>Alice Johnson</strong> vs. Provider: <strong>Modern Living</strong></span>
            </div>
            <div class="dispute-status">
                <span class="status-badge status-pending">Under Review</span>
            </div>
            <div class="action-buttons">
                <a href="#" class="btn-link">View Details</a>
            </div>
        </li>
        <li class="dispute-item">
            <div class="dispute-icon"><i class="fas fa-exclamation-circle" style="color: #e74c3c;"></i></div>
            <div class="dispute-details">
                <p>Dispute #D-002: Payment Discrepancy</p>
                <span>Customer: <strong>Bob Williams</strong> vs. Provider: <strong>Urban Crafters</strong></span>
            </div>
            <div class="dispute-status">
                <span class="status-badge status-pending">Open</span>
            </div>
            <div class="action-buttons">
                <a href="#" class="btn-link">View Details</a>
            </div>
        </li>
    </ul>
</div>

<div class="content-card">
    <h3>Resolved Disputes</h3>
    <ul class="disputes-list">
         <li class="dispute-item">
            <div class="dispute-icon"><i class="fas fa-check-circle" style="color: #27ae60;"></i></div>
            <div class="dispute-details">
                <p>Dispute #D-000: Material Quality Issue</p>
                <span>Customer: <strong>Eve Davis</strong> vs. Provider: <strong>Classic Restorations</strong></span>
            </div>
            <div class="dispute-status">
                <span class="status-badge status-approved">Resolved</span>
            </div>
            <div class="action-buttons">
                <a href="#" class="btn-link">View Details</a>
            </div>
        </li>
    </ul>
</div>

<?php require_once 'admin_footer.php'; ?>