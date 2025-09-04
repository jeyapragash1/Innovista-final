<?php
    // Set the specific title for this page
    $pageTitle = 'Home'; 
    
    // Include the master header. This will also start the session.
    include 'header.php';
?>
<main>
    <!-- =========================================
     HERO SLIDER SECTION
     ========================================= -->
    <section class="hero">
        <div class="hero-slider">
            <div class="hero-slide active" style="background-image: url('assets/images/hero/hero1.jpg');"></div>
            <div class="hero-slide" style="background-image: url('assets/images/hero/hero2.jpg');"></div>
            <div class="hero-slide" style="background-image: url('assets/images/hero/hero3.jpg');"></div>
        </div>
        <div class="hero-overlay"></div>
        <div class="hero-content container">
            <h1 style="color: white;">Transforming Spaces, Restoring Dreams</h1>
            <p>Your one-stop platform for interior design, painting, and restoration services in the Northern Province</p>
            <!-- DYNAMIC LINK -->
            <a href="<?php echo isUserLoggedIn() ? 'services.php' : './login.php'; ?>" class="btn btn-primary">Explore Services</a>
        </div>
        <div class="slider-dots">
            <span class="dot active" data-slide="0"></span>
            <span class="dot" data-slide="1"></span>
            <span class="dot" data-slide="2"></span>
        </div>
    </section>
        
    <!-- =========================================
     HOW IT WORKS SECTION
     ========================================= -->
    <section class="how-it-works page-section">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">1</div>
                    <h3>Discover</h3>
                    <p>Browse portfolios and services from top-rated local professionals.</p>
                </div>
                <div class="step">
                    <div class="step-icon">2</div>
                    <h3>Book & Quote</h3>
                    <p>Schedule appointments, get transparent quotes, and manage bookings.</p>
                </div>
                <div class="step">
                    <div class="step-icon">3</div>
                    <h3>Track & Complete</h3>
                    <p>Monitor your project's progress from start to finish with live updates.</p>
                </div>
            </div>
        </div>
    </section>
     
    <!-- =========================================
     OUR SERVICES SECTION
     ========================================= -->
    <section class="services-section page-section">
        <div class="container">
            <h2 class="section-title">Our Core Services</h2>
            <div class="services-grid">
                <!-- Interior Design Card -->
                <div class="service-card">
                    <img src="assets/images/service_interior.jpg" alt="Interior design of a modern living room">
                    <div class="service-card-content">
                        <h3>Interior Design</h3>
                        <p>From concept to completion, we bring your vision to life. Our experts create beautiful, functional spaces tailored to your lifestyle.</p>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'services.php' : 'login.php'; ?>" class="btn btn-secondary">Learn More</a>
                    </div>
                </div>
                <!-- Painting Card -->
                <div class="service-card">
                    <img src="assets/images/service_paint2.jpg" alt="Professional painter working on a wall">
                    <div class="service-card-content">
                        <h3>Professional Painting</h3>
                        <p>A fresh coat of paint can redefine a room. Our professionals deliver flawless, lasting finishes for both interior and exterior projects.</p>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'services.php' : 'login.php'; ?>" class="btn btn-secondary">Learn More</a>
                    </div>
                </div>
                <!-- Restoration Card -->
                <div class="service-card">
                    <img src="assets/images/service_restoration.jpg" alt="Restored antique wooden furniture">
                    <div class="service-card-content">
                        <h3>Restoration</h3>
                        <p>We breathe new life into your cherished spaces and furniture. Our restoration services preserve the beauty and integrity of your property.</p>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'services.php' : 'login.php'; ?>" class="btn btn-secondary">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
     SHOP OUR PRODUCTS SECTION
     ========================================= -->
    <section class="products-section page-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Complete Your Project</h2>
                <p>Find high-quality products from trusted brands, all in one place. From paints to furniture, get everything you need for your project delivered.</p>
            </div>
            <div class="products-grid">
                <!-- Product 1 -->
                <div class="product-card">
                    <img src="assets/images/port1.jpg" alt="Premium Interior Paint">
                    <div class="product-info">
                        <h4>Premium Interior Paint</h4>
                        <p class="product-category">Painting Supplies</p>
                        <div class="product-price">Starts from $45</div>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'product.php' : 'login.php'; ?>" class="btn btn-secondary">View Product</a>
                    </div>
                </div>
                <!-- Product 2 -->
                <div class="product-card">
                    <img src="assets/images/port2.jpg" alt="Modern Sofa">
                    <div class="product-info">
                        <h4>Modern Velvet Sofa</h4>
                        <p class="product-category">Furniture</p>
                        <div class="product-price">$899</div>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'product.php' : 'login.php'; ?>" class="btn btn-secondary">View Product</a>
                    </div>
                </div>
                <!-- Product 3 -->
                <div class="product-card">
                    <img src="assets/images/port3.jpg" alt="Pendant Lights">
                    <div class="product-info">
                        <h4>Elegant Pendant Lights</h4>
                        <p class="product-category">Lighting</p>
                        <div class="product-price">$120</div>
                        <!-- DYNAMIC LINK -->
                        <a href="<?php echo isUserLoggedIn() ? 'product.php' : 'login.php'; ?>" class="btn btn-secondary">View Product</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
        
    <!-- =========================================
     WHY CHOOSE US SECTION
     ========================================= -->
    <section class="why-choose-us page-section">
        <div class="container">
            <h2 class="section-title">Why Choose Innovista?</h2>
            <div class="features-grid">
                <div class="feature-item">
                    <i class="fas fa-user-shield"></i>
                    <h3>Vetted Professionals</h3>
                    <p>Every service provider is carefully verified for quality, experience, and reliability, ensuring you work with only the best.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3>Transparent Pricing</h3>
                    <p>Receive clear, upfront quotations with no hidden fees. You approve the final cost before any work begins.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-tasks"></i>
                    <h3>Live Project Tracking</h3>
                    <p>Stay informed from start to finish. Follow your project's progress in real-time with stage updates directly from your dashboard.</p>
                </div>
                <div class="feature-item">
                    <i class="fas fa-credit-card"></i>
                    <h3>Secure Payments</h3>
                    <p>Our integrated payment system is fully encrypted. Manage all transactions easily and safely within the Innovista platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
     ACHIEVEMENTS / STATS SECTION
     ========================================= -->
    <section class="achievements">
        <div class="container achievements-grid">
            <div class="achievement-item">
                <h3 class="counter" data-goal="50">0</h3>
                <p>Projects Completed</p>
            </div>
            <div class="achievement-item">
                <h3 class="counter" data-goal="30">0</h3>
                <p>Happy Clients</p>
            </div>
            <div class="achievement-item">
                <h3 class="counter" data-goal="15">0</h3>
                <p>Verified Professionals</p>
            </div>
        </div>
    </section>

    <!-- =========================================
     FEATURED PROVIDERS SECTION
     ========================================= -->
    <section class="featured-providers page-section">
        <div class="container">
            <h2 class="section-title">Meet Our Top Professionals</h2>
            <div class="provider-cards">
                <!-- Provider 1 -->
                <div class="provider-card">
                    <img src="https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=400" alt="Provider 1">
                    <div class="provider-info">
                        <h4>Jeronimo G.</h4>
                        <p>Modern Interior Specialist</p>
                    </div>
                </div>
                <!-- Provider 2 -->
                <div class="provider-card">
                    <img src="https://images.unsplash.com/photo-1611432579402-7037e3e2c1e4?w=600&auto=format&fit=crop&q=60" alt="Provider 2">
                    <div class="provider-info">
                        <h4>Maria S.</h4>
                        <p>Vintage Restoration Expert</p>
                    </div>
                </div>
                <!-- Provider 3 -->
                <div class="provider-card">
                    <img src="https://images.unsplash.com/flagged/photo-1553642618-de0381320ff3?q=80&w=687" alt="Provider 3">
                    <div class="provider-info">
                        <h4>David L.</h4>
                        <p>Precision Painting Pro</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
     TESTIMONIALS SECTION
     ========================================= -->
    <section class="testimonials page-section">
        <div class="container">
            <h2 style="color: white;" class="section-title">What Our Clients Say</h2>
            <div class="testimonial-slider">
                <div class="testimonial-cards">
                    <div class="testimonial-card">"Innovista made finding a reliable interior designer so easy! The entire process was seamless, and the result was beyond my expectations. Highly recommended!"<br><span class="author">- Sarah K., Jaffna</span></div>
                    <div class="testimonial-card">"The restoration work on our antique furniture was incredible. The attention to detail was amazing. A truly professional service from start to finish."<br><span class="author">- Ravi P., Vavuniya</span></div>
                    <div class="testimonial-card">"The painting service was fast, clean, and professional. My home feels completely new. I couldn't be happier with the outcome."<br><span class="author">- David L., Kilinochchi</span></div>
                </div>
            </div>
        </div>
    </section>
<!-- =========================================
     PORTFOLIO / OUR WORK SECTION
     ========================================= -->
<section class="portfolio-section page-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Recent Work</h2>
            <p>A glimpse into the spaces we've transformed.</p>
        </div>
        <div class="portfolio-grid homepage-grid">
            <!-- Portfolio Item 1 -->
            <div class="portfolio-item">
                <img src="assets/images/portfolio-1.jpg" alt="Modern living room with minimalist design">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Modern Living Room</h3>
                        <p>Interior Design</p>
                    </div>
                </div>
            </div>
            <!-- Portfolio Item 2 -->
            <div class="portfolio-item">
                <img src="assets/images/portfolio-2.jpg" alt="Cozy bedroom with fresh blue paint">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Serene Bedroom Repaint</h3>
                        <p>Painting</p>
                    </div>
                </div>
            </div>
            <!-- Portfolio Item 3 (Wide) -->
            <div class="portfolio-item wide">
                <img src="assets/images/portfolio-3.jpg" alt="Complete kitchen restoration with new cabinets and island">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Full Kitchen Restoration</h3>
                        <p>Restoration</p>
                    </div>
                </div>
            </div>
            <!-- Portfolio Item 4 (Wide) -->
            <div class="portfolio-item wide">
                <img src="assets/images/portfolio-4.jpg" alt="Bright and open commercial office space design">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Office Space Concept</h3>
                        <p>Interior Design</p>
                    </div>
                </div>
            </div>
            <!-- Portfolio Item 5 -->
            <div class="portfolio-item">
                <img src="assets/images/portfolio-5.jpg" alt="Antique wooden chair restored and reupholstered">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Antique Chair Refurbish</h3>
                        <p>Restoration</p>
                    </div>
                </div>
            </div>
            <!-- Portfolio Item 6 -->
            <div class="portfolio-item">
                <img src="assets/images/portfolio-6.jpg" alt="Exterior of a house after professional painting">
                <div class="portfolio-overlay">
                    <div class="portfolio-content">
                        <h3>Exterior House Painting</h3>
                        <p>Painting</p>                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- =========================================
     FAQ SECTION
     ========================================= -->
    <section class="faq-section page-section">
        <div class="container">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <div class="faq-accordion">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I book a service?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Booking is simple! Just browse our services, select a professional you like, view their availability on the real-time calendar, and choose a time that works for you. You'll receive an instant email confirmation.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Are the service providers verified?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Absolutely. Every service provider on Innovista goes through a verification process where we check their experience, past work, and reliability. We only partner with trusted, high-quality professionals to ensure your peace of mind.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How does payment work?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Our system is designed for transparency and security. Based on the quotation you approve, you make a 25% advance payment to secure your booking. The remaining balance is only due after the project is completed to your satisfaction. All transactions are handled securely through our platform.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can I track my project's progress?</span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! Our Live Project Tracking feature allows you to monitor your project's progress through your customer dashboard. Service providers upload stage-wise updates and photos, so you're always in the loop from start to finish.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
     JOIN OUR COMMUNITY (CTA) SECTION
     ========================================= -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Your Next Project?</h2>
                <p>Whether you're looking to transform your home or grow your service business, the Innovista community is here for you. Join today for a seamless, transparent, and trustworthy experience.</p>
                <div class="cta-buttons">
                    <!-- DYNAMIC LINK -->
                    <a href="<?php echo isUserLoggedIn() ? 'services.php' : 'login.php'; ?>" class="btn btn-primary">Find a Professional</a>
                    <!-- DYNAMIC LINK -->
                    <a href="<?php echo isUserLoggedIn() ? 'provider_dashboard.php' : 'signup.php'; ?>" class="btn btn-secondary">Join as a Provider</a>
                </div>
            </div>
        </div>
    </section>

</main>   

<?php include './footer.php'; ?>