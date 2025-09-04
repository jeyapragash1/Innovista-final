<?php
// Define the page title for this specific page
$pageTitle = 'About Us'; 
// Include the master header
include 'header.php'; 
?>

<!-- =========================================
     MAIN CONTENT FOR ABOUT PAGE
     ========================================= -->
<main>
    <!-- 1. Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1>About Innovista</h1>
            <p>Building a seamless connection between vision and craftsmanship in the Northern Province.</p>
        </div>
    </section>

    <!-- 2. Our Mission Section -->
    <section class="our-mission page-section">
        <div class="container mission-grid">
            <div class="mission-image">
                <img src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1170" alt="Well-designed interior space">
            </div>
            <div class="mission-text">
                <h2>Our Story</h2>
                <p>Innovista was born from a simple observation: finding reliable, high-quality interior design and restoration services in Sri Lanka's Northern Province was a significant challenge. Homeowners struggled to connect with vetted professionals, and talented artisans lacked a modern platform to showcase their skills and grow their business.</p>
                <p>Our mission is to bridge this gap with technology. We've created a trusted digital marketplace that empowers both customers and service providers, making it easier than ever to bring beautiful and functional design projects to life through a transparent and efficient process.</p>
            </div>
        </div>
    </section>

    <!-- =========================================
         NEW: OUR VALUES SECTION
         ========================================= -->
    <section class="our-values page-section">
        <div class="container">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-handshake"></i></div>
                    <h3>Trust & Transparency</h3>
                    <p>We build trust through verified providers, clear quotations, and a secure payment system. What you see is what you get.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-gem"></i></div>
                    <h3>Commitment to Quality</h3>
                    <p>We are dedicated to excellence. Every professional on our platform is vetted to ensure the highest standards of craftsmanship.</p>
                </div>
                <div class="value-item">
                    <div class="value-icon"><i class="fas fa-lightbulb"></i></div>
                    <h3>Innovation</h3>
                    <p>We leverage technology to simplify the entire process, from initial discovery and booking to live project tracking.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Our Team Section (Updated with real names) -->
    <section class="our-team page-section">
        <div class="container">
            <h2 class="section-title">Meet the Team</h2>
            <p class="section-subtitle">The passionate minds behind Innovista.</p>
            <div class="team-grid">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <img src="assets/images/about/mem1.jpg" alt="J.Denusha">
                    <h4>J.Denusha</h4>
                    <p>Project Manager</p>
                </div>
                <!-- Team Member 2 -->
                <div class="team-member">
                    <img src="assets/images/about/mem2.jpg" alt="S.Kristo Praveejiny">
                    <h4>S.Kristo Praveejiny</h4>
                    <p>Lead UI/UX Designer</p>
                </div>
                <!-- Team Member 3 -->
                <div class="team-member">
                    <img src="assets/images/about/mem3.jpg" alt="R.Prathijusha">
                    <h4>R.Prathijusha</h4>
                    <p>Lead Developer</p>
                </div>
                <!-- Team Member 4 -->
                 <div class="team-member">
                    <img src="assets/images/about/mem4.jpg" alt="R.Jathushan">
                    <h4>R.Jathushan</h4>
                    <p>Database Architect</p>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================
         NEW: CTA SECTION
         ========================================= -->
    <section class="cta-section about-cta">
        <div class="container">
            <h2>Ready to Transform Your Space?</h2>
            <p>Explore our services and find the perfect professional for your next project.</p>
            <a href="services.php" class="btn btn-primary">View Our Services</a>
        </div>
    </section>
        
</main>

<?php 
// Include the master footer
include 'footer.php'; 
?>