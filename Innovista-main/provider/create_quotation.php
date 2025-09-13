<?php
require_once '../config/session.php';
protectPage('provider');
require_once '../config/Database.php';
require_once '../provider/provider_header.php'; 

$quotation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$quotation_id) {
	echo '<div style="color:red;">Invalid quotation ID.</div>';
	exit();
}
$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT q.*, u.name as customer_name FROM quotations q JOIN users u ON q.customer_id = u.id WHERE q.id = :id');
$stmt->bindParam(':id', $quotation_id);
$stmt->execute();
$quote = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$quote) {
	echo '<div style="color:red;">Quotation not found.</div>';
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Create & Send Quotation</title>
	<link rel="stylesheet" href="../public/assets/css/create_quotation.css">
</head>
<body>
<div class="quotation-container">
	<h2>Create & Send Quotation</h2>
	<div class="quotation-details">
		   <div><strong>Customer Name:</strong> <?php echo htmlspecialchars($quote['customer_name']); ?></div>
		   <div><strong>Service:</strong> 
			   <?php
			   $service = $quote['service_type'];
			   $subcategory = isset($quote['subcategory']) ? $quote['subcategory'] : '';
			   echo htmlspecialchars($service);
			   if ($subcategory) {
				   echo ' — <span style="color:#1eb6e9;">' . htmlspecialchars($subcategory) . '</span>';
			   }
			   ?>
		   </div>
		   <div><strong>Description:</strong> <?php echo htmlspecialchars($quote['project_description']); ?></div>
		<?php if (!empty($quote['photos'])): ?>
			<div class="quotation-images"><strong>Images:</strong><br>
				<?php foreach (explode(',', $quote['photos']) as $img): ?>
					<img src="../uploads/<?php echo htmlspecialchars($img); ?>">
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<form class="styled-form" method="POST" action="save_quotation.php" enctype="multipart/form-data">
		<input type="hidden" name="quotation_id" value="<?php echo $quotation_id; ?>">
		<div class="form-row">
			<div class="form-group">
				<label for="amount">Quotation Amount (₹):</label>
				<input type="number" name="amount" id="amount" required min="0" step="0.01" placeholder="Enter total amount">
			</div>
			<div class="form-group">
				<label for="advance">Advance (25%):</label>
				<input type="number" name="advance" id="advance" readonly placeholder="Auto-calculated">
			</div>
			<div class="form-group">
				<label for="extra_amount">Extra Amount (₹):</label>
				<input type="number" name="extra_amount" id="extra_amount" min="0" step="0.01" placeholder="Auto-calculated" readonly>
			</div>
		</div>
		<div class="timeline-row">
			<div>
				<label for="start_date">Start Date:</label>
				<input type="date" name="start_date" id="start_date" required>
			</div>
			<div>
				<label for="end_date">Estimated End Date:</label>
				<input type="date" name="end_date" id="end_date" required>
			</div>
		</div>
		<label for="validity">Validity Period (days):</label>
		<input type="number" name="validity" id="validity" required min="1" placeholder="e.g. 30">
		<label for="provider_notes">Provider Extra Notes:</label>
		<textarea name="provider_notes" id="provider_notes" rows="3" placeholder="Add any extra notes or details..."></textarea>
		<button type="submit">Send Quotation</button>
	</form>
</div>
<script>
(function(){
	var amountEl = document.getElementById('amount');
	var advanceEl = document.getElementById('advance');
	var extraEl = document.getElementById('extra_amount');

	function toMoney(n){
		return (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);
	}

	function recalc(){
		var amt = parseFloat(amountEl.value);
		if (isNaN(amt) || amt < 0) { amt = 0; }
		var adv = amt * 0.25; // 25% advance
		advanceEl.value = toMoney(adv);
		var extra = amt - adv;
		if (extra < 0) extra = 0;
		extraEl.value = toMoney(extra);
	}

	amountEl.addEventListener('input', recalc);
	document.addEventListener('DOMContentLoaded', recalc);
})();
</script>
<style>
.form-row { display: flex; gap: 24px; margin-bottom: 18px; }
.form-group { flex: 1; display: flex; flex-direction: column; }
.form-group label { font-weight: 500; margin-bottom: 6px; }
.form-group input { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccc; }
</style>
</script>
</body>
</html>
