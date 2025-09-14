<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\customer\request_quotation.php

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

$pageTitle = 'Request a New Quote';
require_once '../includes/user_dashboard_header.php'; 
require_once '../config/Database.php';

$db = (new Database())->getConnection();

// Get optional provider_id from GET parameters (if linked from provider profile)
$preselected_provider_id = filter_input(INPUT_GET, 'provider_id', FILTER_VALIDATE_INT);
$provider_name_display = '';

// If a provider ID is present, fetch their name for display
if ($preselected_provider_id) {
    try {
        $stmt_provider = $db->prepare("SELECT name FROM users WHERE id = :provider_id AND role = 'provider'");
        $stmt_provider->bindParam(':provider_id', $preselected_provider_id, PDO::PARAM_INT);
        $stmt_provider->execute();
        $provider_data = $stmt_provider->fetch(PDO::FETCH_ASSOC);
        if ($provider_data) {
            $provider_name_display = htmlspecialchars($provider_data['name']);
        } else {
            // Provider not found or not a provider, unset ID
            $preselected_provider_id = null;
            set_flash_message('info', 'Selected provider not found or invalid. You can still submit a general request.');
        }
    } catch (PDOException $e) {
        error_log("Error fetching provider data in request_quotation.php: " . $e->getMessage());
        set_flash_message('error', 'Error loading provider data. Please try again.');
        $preselected_provider_id = null;
    }
}

// Fetch available main service types (from 'service' table or a predefined list)
// For now, using predefined list as service table is provider-specific
$service_types = [
    'Interior Design',
    'Painting',
    'Restoration'
];

?>

<?php display_flash_message(); ?>

<h2>Request a New Quotation</h2>
<p>Fill out the form below to get a price for your next project from our providers.</p>

<div class="content-card">
    <form action="../handlers/handle_quote_request.php" method="POST" class="form-section" enctype="multipart/form-data">
        <?php if ($preselected_provider_id): ?>
            <div class="alert alert-info">
                You are requesting a quote specifically from: <strong><?php echo $provider_name_display; ?></strong>
                <input type="hidden" name="provider_id" value="<?php echo htmlspecialchars($preselected_provider_id); ?>">
            </div>
        <?php else: ?>
            <p>Your request will be sent to available providers based on your selected service type.</p>
        <?php endif; ?>

        <div class="form-group">
            <label for="service_type">Select Service Type</label>
            <select id="service_type" name="service_type" required>
                <option value="" disabled selected>-- Choose a service --</option>
                <?php foreach ($service_types as $type): ?>
                    <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="project_description">Project Description</label>
            <textarea id="project_description" name="project_description" placeholder="Please describe your project in detail. Include room dimensions, desired style, and any specific requirements." required></textarea>
        </div>
        <div class="form-group">
            <label for="attachments">Upload Photos (Optional)</label>
            <input type="file" id="attachments" name="attachments[]" multiple accept="image/*">
            <small>You can upload multiple images (JPG, PNG, GIF). Max 2MB per image.</small>
        </div>
        
        <button type="submit" class="btn-submit">Submit Request</button>
    </form>
</div>
<?php require_once '../includes/user_dashboard_footer.php'; ?>