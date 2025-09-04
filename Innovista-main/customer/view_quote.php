<?php
require_once '../config/session.php';
protectPage('customer');

$pageTitle = 'View Quotation';
require_once '../includes/user_dashboard_header.php';
require_once '../config/Database.php';

$quotation_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$quotation_id) {
    echo "<h2>Invalid Quotation ID</h2>";
    require_once '../includes/user_dashboard_footer.php';
    exit();
}

// In a real app, query the database for quote details// Fetch real quotation details from DB
// Try to fetch custom quotation first
$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT cq.*, u.name as provider_name FROM custom_quotations cq JOIN users u ON cq.provider_id = u.id WHERE cq.quotation_id = :id');
$stmt->bindParam(':id', $quotation_id);
$stmt->execute();
$custom_quote = $stmt->fetch(PDO::FETCH_ASSOC);
if ($custom_quote) {
    $quote = $custom_quote;
    $is_custom = true;
} else {
    // Fallback to original quotation
    $stmt = $db->prepare('SELECT q.*, u.name as provider_name FROM quotations q JOIN users u ON q.provider_id = u.id WHERE q.id = :id');
    $stmt->bindParam(':id', $quotation_id);
    $stmt->execute();
    $quote = $stmt->fetch(PDO::FETCH_ASSOC);
    $is_custom = false;
    if (!$quote) {
        echo '<h2>Quotation not found.</h2>';
        require_once '../includes/user_dashboard_footer.php';
        exit();
    }
}
?>

<h2>Review Quotation</h2>
<p>Please review the details of the quotation below and take action.</p>

<div class="content-card">
    <h3>Quotation Details</h3>
    <div class="details-list">
        <p><strong>Provider:</strong> <?php echo htmlspecialchars($quote['provider_name']); ?></p>
        <p><strong>Project:</strong> <?php echo htmlspecialchars($quote['project_description']); ?></p>
        <p><strong>Status:</strong> <span class="status-badge status-pending"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $quote['status']))); ?></span></p>
        <?php if ($is_custom): ?>
            <p><strong>Quoted Price:</strong> <span style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color);">₹<?php echo number_format($quote['amount'], 2); ?></span></p>
            <p><strong>Advance (25%):</strong> ₹<?php echo number_format($quote['advance'], 2); ?></p>
            <p><strong>Timeline:</strong> <?php echo htmlspecialchars($quote['start_date']); ?> to <?php echo htmlspecialchars($quote['end_date']); ?></p>
            <p><strong>Validity Period:</strong> <?php echo htmlspecialchars($quote['validity']); ?> days</p>
            <?php if (!empty($quote['photos'])): ?>
                <div class="quotation-images"><strong>Images:</strong><br>
                    <?php foreach (explode(',', $quote['photos']) as $img): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($img); ?>" style="max-width:100px; margin:5px; border-radius:6px;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <p><strong>Quoted Price:</strong> <span style="font-size: 1.5rem; font-weight: 600; color: var(--primary-color);">-</span></p>
            <p><strong>Advance (25%):</strong> -</p>
            <p><strong>Timeline:</strong> -</p>
            <p><strong>Validity Period:</strong> -</p>
        <?php endif; ?>
    </div>

    <h3 style="margin-top: 2rem;">Provider Notes</h3>
    <p><?php echo $is_custom ? htmlspecialchars($quote['provider_notes']) : '-'; ?></p>

    <div class="action-buttons" style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; display: flex; gap: 16px;">
        <button type="button" class="btn-submit" id="btnConfirmBooking" style="background-color: #27ae60; width:100%;">Confirm Booking</button>
        <form action="../handlers/handle_quote_action.php" method="POST" style="flex:1;">
            <input type="hidden" name="quotation_id" value="<?php echo $quotation_id; ?>">
            <button type="submit" name="action" value="cancel" class="btn-submit" style="background-color: #c0392b; width:100%;">Cancel</button>
        </form>
    </div>
</div>

<!-- Modal container for iframe -->
<div id="bookingModalContainer" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
    <div style="position:relative; width:420px; max-width:95vw; margin:auto;">
        <iframe id="bookingModalIframe" style="width:100%; height:600px; border:none; border-radius:18px; background:#fff;"></iframe>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var confirmBtn = document.getElementById('btnConfirmBooking');
    var modalContainer = document.getElementById('bookingModalContainer');
    var iframe = document.getElementById('bookingModalIframe');
    var serviceName = "<?php echo isset($quote['project_description']) ? htmlspecialchars($quote['project_description']) : ''; ?>";
    console.log('Service name for modal:', serviceName);
    if (confirmBtn && modalContainer && iframe) {
        confirmBtn.addEventListener('click', function() {
            iframe.src = "booking-modal.html?service=" + encodeURIComponent(serviceName);
            modalContainer.style.display = 'flex';
        });
        window.addEventListener('click', function(e) {
            if (e.target === modalContainer) {
                modalContainer.style.display = 'none';
            }
        });
    }
});
window.addEventListener('message', function(event) {
    if (event.data === 'closeBookingModal') {
        var modalContainer = document.getElementById('bookingModalContainer');
        if (modalContainer) {
            modalContainer.style.display = 'none';
        }
    }
}, false);
</script>

<?php require_once '../includes/user_dashboard_footer.php'; ?>