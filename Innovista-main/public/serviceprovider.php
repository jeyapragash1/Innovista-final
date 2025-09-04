<?php if (!empty($provider['provider_bio'])): ?>
                        <div class="provider-bio" style="margin-top:1rem;">
                            <strong>Bio / Description:</strong> <?php echo htmlspecialchars($provider['provider_bio']); ?>
                        </div>
                    <?php endif; ?>
<?php 
$pageTitle = 'Find a Professional';
include 'header.php'; 
require_once __DIR__ . '/../config/Database.php';

// Get selected service from query string, e.g., ?service=Interior%20Design
$selectedService = isset($_GET['service']) ? $_GET['service'] : '';
$selectedSubcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';

$database = new Database();
$db = $database->getConnection();

$providers = [];
if ($selectedService && $selectedSubcategory) {
    // Filter by main_service and subcategory
    $stmt = $db->prepare('SELECT * FROM service WHERE main_service = :service AND subcategories LIKE :subcategory');
    $stmt->bindParam(':service', $selectedService);
    $likeSubcat = "%" . $selectedSubcategory . "%";
    $stmt->bindParam(':subcategory', $likeSubcat);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($selectedService) {
    // Filter by main_service only
    $stmt = $db->prepare('SELECT * FROM service WHERE main_service = :service');
    $stmt->bindParam(':service', $selectedService);
    $stmt->execute();
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no service selected, show nothing
    $providers = [];
}
?>


<header class="provider-page-header">
    <div class="container">
        <h1>Find Your Perfect Professional</h1>
        <p>Browse our list of verified experts to find the right match for your project.</p>
    </div>
</header>

<?php
// Show flash message if present
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['flash_message'])) {
    $msg = $_SESSION['flash_message'];
    if (is_array($msg) && isset($msg['message'])) {
        echo '<div style="background:#e0ffe0; color:#2d7a2d; padding:1rem; margin:1rem 0; border-radius:6px; text-align:center; font-size:1.1rem;">'.htmlspecialchars($msg['message']).'</div>';
    } else {
        echo '<div style="background:#e0ffe0; color:#2d7a2d; padding:1rem; margin:1rem 0; border-radius:6px; text-align:center; font-size:1.1rem;">'.htmlspecialchars($msg).'</div>';
    }
    unset($_SESSION['flash_message']);
}
?>
<main class="provider-listing-layout container page-section">
    <!-- Filters Sidebar -->
    <aside class="provider-filters">
        <!-- ... (Your filter HTML is correct) ... -->
    </aside>

    <!-- Provider List -->
    <section class="provider-list-container">
        <?php if (empty($providers)): ?>
            <div style="padding:2rem; text-align:center; color:#888; font-size:1.2rem;">No providers found for this service.</div>
        <?php else: ?>
            <?php foreach ($providers as $provider): ?>
            <div class="provider-card-list">
                <div class="provider-details">
                    <h3 class="provider-name">
                        <?php echo htmlspecialchars($provider['provider_name']); ?>
                        <i class="fas fa-check-circle verified-badge" title="Verified Provider"></i>
                    </h3>
                    <div class="service-tags-list">
                        <span class="service-tag-item"><?php echo htmlspecialchars($provider['main_service']); ?></span>
                        <?php 
                        $subcats = explode(',', $provider['subcategories']);
                        foreach ($subcats as $subcat): ?>
                            <span class="service-tag-item"><?php echo htmlspecialchars(trim($subcat)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="provider-contact-details" style="margin-top:1rem;">
                        <strong>Email:</strong> <?php echo htmlspecialchars($provider['provider_email']); ?><br>
                        <strong>Phone:</strong> <?php echo htmlspecialchars($provider['provider_phone']); ?><br>
                        <strong>Address:</strong> <?php echo htmlspecialchars($provider['provider_address']); ?><br>
                    </div>
                    <?php if (!empty($provider['portfolio'])): ?>
                        <div class="provider-portfolio-gallery" style="margin-top:1rem;">
                            <strong>Portfolio:</strong><br>
                            <?php 
                            $portfolio = explode(',', $provider['portfolio']);
                            foreach ($portfolio as $photo): ?>
                                <img src="../public/assets/images/<?php echo htmlspecialchars(trim($photo)); ?>" alt="Portfolio" style="max-width:80px;max-height:80px;margin:4px;border-radius:6px;border:1px solid #eee;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="provider-actions">
                    <button type="button" class="btn btn-primary btn-book-consultation">Book Consultation</button>
                    <form class="quote-request-form" data-provider-id="<?php echo htmlspecialchars($provider['provider_id']); ?>" data-service-type="<?php echo htmlspecialchars($provider['main_service']); ?>" data-subcategory="<?php echo isset($_GET['subcategory']) ? htmlspecialchars($_GET['subcategory']) : ''; ?>" data-project-description="Request for <?php echo htmlspecialchars($provider['main_service']); ?> - <?php echo isset($_GET['subcategory']) ? htmlspecialchars($_GET['subcategory']) : ''; ?>" style="display:inline;">
                        <button type="button" class="btn btn-secondary btn-request-quote">Request a Quote</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

<!-- Quote Request Modal -->
<div id="quoteRequestModal" class="booking-modal">
    <div class="booking-modal-content" style="max-width:500px;">
        <span class="close-modal-btn">×</span>
        <h2 style="margin-bottom:1rem;">Request a Quotation</h2>
        <form id="quotePreviewForm">
            <div class="form-group">
                <label for="previewProjectDescription"><strong>Project Description</strong></label>
                <textarea id="previewProjectDescription" rows="5" style="width:100%;padding:0.5rem;" required placeholder="Please describe your project in detail. Include room dimensions, desired style, and any specific requirements."></textarea>
            </div>
            <div class="form-group">
                <label><strong>Upload Photos (Optional)</strong></label>
                <input type="file" id="previewUploadPhotos" multiple>
                <div id="previewFileList" style="margin-top:0.5rem;"></div>
            </div>
            <button type="submit" class="btn btn-primary" id="submitQuoteBtn" style="margin-top:1rem;">Submit Request</button>
        </form>
    </div>
</div>
</main>



<div id="bookingModal" class="booking-modal">
    <div class="booking-modal-content">
        <span class="close-modal-btn">×</span>
        <div id="calendarStep">
            <div style="display:flex;justify-content:center;align-items:center;margin-bottom:8px;gap:12px;">
                <button id="prevMonthBtn" style="background:#e5e7eb;border:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-weight:600;">&#8592;</button>
                <span id="calendarMonthTitle" style="font-weight:600;font-size:1.1rem;min-width:120px;text-align:center;"></span>
                <button id="nextMonthBtn" style="background:#e5e7eb;border:none;border-radius:6px;padding:6px 12px;cursor:pointer;font-weight:600;">&#8594;</button>
            </div>
            <div id="calendar-container"></div>
        </div>
        <div id="paymentStep" style="display:none;">
            <h3>Confirm & Pay Consultation Fee</h3>
            <p>A $50 fee is required to confirm your booking. This will be credited towards your project.</p>
            <form action="#" class="payment-form">
                <div class="form-group">
                    <label for="cardholder-name">Cardholder Name</label>
                    <input type="text" id="cardholder-name" placeholder="John M. Doe" required>
                </div>
                <div class="form-group">
                    <label for="card-number">Card Number</label>
                    <input type="text" id="card-number" placeholder="•••• •••• •••• ••••" required>
                </div>
                <div class="card-details">
                    <div class="form-group">
                        <label for="expiry-date">Expiry</label>
                        <input type="text" id="expiry-date" placeholder="MM / YY" required>
                    </div>
                    <div class="form-group">
                        <label for="cvc">CVC</label>
                        <input type="text" id="cvc" placeholder="CVC" required>
                    </div>
                     <div class="form-group">
                        <label for="zip">ZIP</label>
                        <input type="text" id="zip" placeholder="ZIP Code" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-confirm-booking">Pay $50 & Confirm Booking</button>
            </form>
            <a href="#" id="backToCalendar" class="back-to-calendar">← Back to Calendar</a>
        </div>
    </div>
</div>
<script src="assets/js/serviceprovider.js"></script>
<?php 
// Include the footer, which now should also link to our new script
include 'footer.php'; 
?>