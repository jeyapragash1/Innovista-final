<?php
require_once '../config/session.php'; 
protectPage('customer');

$pageTitle = 'Request a New Quote';
require_once '../includes/user_dashboard_header.php'; 
?>
<?php display_flash_message(); ?>
<h2>Request a New Quotation</h2>
<p>Fill out the form below to get a price for your next project from our providers.</p>

<div class="content-card">
    <form action="../handlers/handle_quote_request.php" method="POST" class="form-section" enctype="multipart/form-data">
        <div class="form-group">
            <label for="service_type">Select Service Type</label>
            <select id="service_type" name="service_type" required>
                <option value="" disabled selected>-- Choose a service --</option>
                <option value="interior_design">Interior Design</option>
                <option value="painting">Painting</option>
                <option value="restoration">Restoration</option>
            </select>
        </div>
        <div class="form-group">
            <label for="project_description">Project Description</label>
            <textarea id="project_description" name="project_description" placeholder="Please describe your project in detail. Include room dimensions, desired style, and any specific requirements." required></textarea>
        </div>
        <div class="form-group">
            <label for="attachments">Upload Photos (Optional)</label>
            <input type="file" id="attachments" name="attachments[]" multiple>
        </div>
        <?php if (isset($_GET['provider_id']) && intval($_GET['provider_id']) > 0): ?>
    <input type="hidden" name="provider_id" value="<?php echo intval($_GET['provider_id']); ?>">
    <button type="submit" class="btn-submit">Submit Request</button>
<?php else: ?>
    <div style="color:red; font-weight:bold; margin-bottom:12px;">Error: No provider selected. Please go back and select a provider.</div>
    <button type="submit" class="btn-submit" disabled>Submit Request</button>
<?php endif; ?>
    </form>
</div>
<?php require_once '../includes/user_dashboard_footer.php'; ?>