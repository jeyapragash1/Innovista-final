<?php
// Define the page title for this specific page
$pageTitle = 'Our Services'; 
// Include the master header, which also starts the session
include 'header.php'; 

// Define the target URL based on login status. This makes the code cleaner.
$targetUrl = isUserLoggedIn() ? './serviceprovider.php' : './login.php';
?>

<!-- =========================================
     SERVICES HERO SECTION
     ========================================= -->
<section class="services-hero">
    <div class="hero-slider">
        <div class="hero-slide active" style="background-image: url('assets/images/images/service/head1.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/images/service/head2.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/images/service/head3.jpg');"></div>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Our Premium Services</h1>
        <p>Transform your space with our expert interior design, painting, and restoration services in the Northern Province.</p>
        <div class="hero-stats">
            <div class="stat">
                <span class="stat-number">50+</span>
                <span class="stat-label">Expert Providers</span>
            </div>
            <div class="stat">
                <span class="stat-number">4.8 ★</span>
                <span class="stat-label">Customer Rating</span>
            </div>
            <div class="stat">
                <span class="stat-number">500+</span>
                <span class="stat-label">Projects Completed</span>
            </div>
        </div>
    </div>
</section>

<!-- =========================================
     MAIN SERVICES LAYOUT
     ========================================= -->
<main class="services-layout container page-section">
    <aside class="service-sidebar">
        <h3 class="sidebar-title">Service Categories</h3>
        <button class="service-tab active" data-service="interior-design"><i class="fas fa-couch"></i> Interior Design</button>
        <button class="service-tab" data-service="painting"><i class="fas fa-paint-roller"></i> Professional Painting</button>
        <button class="service-tab" data-service="restoration"><i class="fas fa-hammer"></i> Restoration</button>
    </aside>
  
    <section class="services-content-area">
        <!-- Interior Design Card (Initially visible) -->
        <div class="service-card active" data-service="interior-design">
            <div class="service-header-box">
                <h3>Interior Design</h3>
                <p>From concept to completion, we create spaces that are both beautiful and functional. Choose individual services or our all-inclusive package.</p>
            </div>
            <div class="subcategory-grid">
                <div class="subcategory-card"><img src="assets/images/images/service/ceiling.jpg" alt="Ceiling & Lighting"><h4>Ceiling & Lighting</h4><p>Modern ceiling and lighting solutions.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Ceiling%20%26%20Lighting" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/interior 1.jpg" alt="Space Planning"><h4>Space Planning</h4><p>Efficient and functional layouts.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Space%20Planning" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/kitchen.avif" alt="Modular Kitchen"><h4>Modular Kitchen</h4><p>Smart and stylish kitchen designs.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Modular%20Kitchen" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/bathroom.jpg" alt="Bathroom Design"><h4>Bathroom Design</h4><p>Elegant and relaxing bathroom spaces.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Bathroom%20Design" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/wood.jpg" alt="Carpentry"><h4>Carpentry & Woodwork</h4><p>Custom woodwork with fine finishes.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Carpentry%20%26%20Woodwork" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/furniture.jpg" alt="Furniture Design"><h4>Furniture Design</h4><p>Unique and comfortable furnishings.</p><a href="serviceprovider.php?service=Interior%20Design&subcategory=Furniture%20Design" class="get-service-link">Get Service →</a></div>
            </div>
            <div class="package-section">
                <h4>Complete Interior Package</h4>
                <p>Get all our interior design expertise bundled at a discounted rate for a seamless transformation.</p>
                <button class="btn btn-primary btn-get-package" onclick="window.location.href='<?php echo $targetUrl; ?>'">Book a Consultation</button>
            </div>
        </div>

        <!-- Painting Card (Initially hidden) -->
        <div class="service-card" data-service="painting">
            <div class="service-header-box">
                <h3>Professional Painting</h3>
                <p>Expert painting for residential and commercial spaces, ensuring a flawless finish that lasts.</p>
            </div>
            <div class="subcategory-grid">
                <div class="subcategory-card"><img src="assets/images/images/service/interiorpaint.jpg" alt="Interior Painting"><h4>Interior Painting</h4><p>Beautiful and durable indoor finishes.</p><a href="serviceprovider.php?service=Painting&subcategory=Interior%20Painting" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/exterior.jpg" alt="Exterior Painting"><h4>Exterior Painting</h4><p>Weatherproof and lasting exterior color.</p><a href="serviceprovider.php?service=Painting&subcategory=Exterior%20Painting" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/water.jpg" alt="Water Proofing"><h4>Water & Damp Proofing</h4><p>Protect your walls from moisture damage.</p><a href="serviceprovider.php?service=Painting&subcategory=Water%20%26%20Damp%20Proofing" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/commercial.webp" alt="Commercial Painting"><h4>Commercial Painting</h4><p>Professional finishes for your business.</p><a href="serviceprovider.php?service=Painting&subcategory=Commercial%20Painting" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/art.jpg" alt="Wall Art & Murals"><h4>Wall Art & Murals</h4><p>Creative custom designs and murals.</p><a href="serviceprovider.php?service=Painting&subcategory=Wall%20Art%20%26%20Murals" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/paintconsul.jpg" alt="Color Consultation"><h4>Color Consultation</h4><p>Expert advice on picking the perfect palette.</p><a href="serviceprovider.php?service=Painting&subcategory=Color%20Consultation" class="get-service-link">Get Service →</a></div>
            </div>
            <div class="package-section">
                <h4>Complete Painting Package</h4>
                <p>Bundle interior, exterior, and waterproofing services for a comprehensive painting solution.</p>
                <button class="btn btn-primary btn-get-package" onclick="window.location.href='<?php echo $targetUrl; ?>'">Request a Quote</button>
            </div>
        </div>
      
        <!-- Restoration Card (Initially hidden) -->
        <div class="service-card" data-service="restoration">
            <div class="service-header-box">
                <h3>Restoration Services</h3>
                <p>Breathe new life into your property. We restore and preserve the beauty of your spaces and furniture.</p>
            </div>
            <div class="subcategory-grid">
                <div class="subcategory-card"><img src="assets/images/images/service/wallreno.jpg" alt="Wall Repairs"><h4>Wall Repairs & Plastering</h4><p>Seamless wall and ceiling restoration.</p><a href="serviceprovider.php?service=Restoration&subcategory=Wall%20Repairs%20%26%20Plastering" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/floorreno.jpg" alt="Floor Restoration"><h4>Floor Restoration</h4><p>Refinishing and repairs for all floor types.</p><a href="serviceprovider.php?service=Restoration&subcategory=Floor%20Restoration" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/doorreno.jpg" alt="Carpentry Repairs"><h4>Door & Window Repairs</h4><p>Expert restoration of wooden fixtures.</p><a href="serviceprovider.php?service=Restoration&subcategory=Door%20%26%20Window%20Repairs" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/spacereno.jpg" alt="Old Space Transformation"><h4>Old Space Transformation</h4><p>Modernize your interiors completely.</p><a href="serviceprovider.php?service=Restoration&subcategory=Old%20Space%20Transformation" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/furniture.jpg" alt="Furniture Restoration"><h4>Furniture Restoration</h4><p>Restore antique and modern furniture.</p><a href="serviceprovider.php?service=Restoration&subcategory=Furniture%20Restoration" class="get-service-link">Get Service →</a></div>
                <div class="subcategory-card"><img src="assets/images/images/service/roomreno.jpg" alt="Building Renovation"><h4>Full Building Renovation</h4><p>Complete structural and aesthetic renewal.</p><a href="serviceprovider.php?service=Restoration&subcategory=Full%20Building%20Renovation" class="get-service-link">Get Service →</a></div>
            </div>
            <div class="package-section">
                <h4>Complete Restoration Package</h4>
                <p>A full-service solution to completely renovate and restore your property from top to bottom.</p>
                <button class="btn btn-primary btn-get-package" onclick="window.location.href='<?php echo $targetUrl; ?>'">Get an Estimate</button>
            </div>
        </div>
    </section>
</main>

<!-- =========================================
     NEW: OUR COMMITMENT TO QUALITY SECTION
     ========================================= -->
<section class="commitment-section page-section">
    <div class="container">
        <h2 class="section-title">Our Commitment to Quality</h2>
        <div class="commitment-grid">
            <div class="commitment-item">
                <div class="commitment-icon"><i class="fas fa-tachometer-alt"></i></div>
                <h3>Performance</h3>
                <p>Our platform is optimized to load pages within 3 seconds, ensuring a fast and frustration-free experience for you.</p>
            </div>
            <div class="commitment-item">
                <div class="commitment-icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Security</h3>
                <p>Your data is safe with us. All sensitive information, from passwords to payments, is encrypted to protect you.</p>
            </div>
            <div class="commitment-item">
                <div class="commitment-icon"><i class="fas fa-check-circle"></i></div>
                <h3>Reliability</h3>
                <p>We aim for 99% uptime with automatic backups, so Innovista is always available when you need it.</p>
            </div>
            <div class="commitment-item">
                <div class="commitment-icon"><i class="fas fa-handshake"></i></div>
                <h3>Transparency</h3>
                <p>From verified providers to clear invoices and a public feedback system, we build trust every step of the way.</p>
            </div>
        </div>
    </div>
</section>

<!-- =========================================
     NEW: PRODUCTS CTA SECTION
     ========================================= -->
<section class="products-cta-section">
    <div class="container">
        <h2>Don't Forget the Finishing Touches</h2>
        <p>In addition to our expert services, we offer a curated selection of high-quality products to complete your project.</p>
        <a href="./product.php" class="btn btn-primary">Shop Our Products</a>
    </div>
</section>

<?php 
include 'footer.php'; 
?>