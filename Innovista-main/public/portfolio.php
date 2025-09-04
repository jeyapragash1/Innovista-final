<?php
// Define the page title for this specific page
$pageTitle = 'Our Work'; 
// Include the master header, which also starts the session
include 'header.php'; 
?>

<!-- =========================================
     MAIN CONTENT FOR PORTFOLIO PAGE
     ========================================= -->
<main>
    <!-- 1. Page Header/Banner -->
    <section class="portfolio-header">
        <div class="container">
            <h1>Our Work</h1>
            <p>Explore a selection of our finest design, painting, and restoration projects that showcase our commitment to quality and craftsmanship.</p>
        </div>
    </section>

    <!-- 2. Portfolio Grid Section with NEW Filter Buttons -->
    <section class="portfolio-showcase page-section">
        <div class="container">
            <!-- NEW: Filter Buttons -->
            <div class="portfolio-filters">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="design">Interior Design</button>
                <button class="filter-btn" data-filter="painting">Painting</button>
                <button class="filter-btn" data-filter="restoration">Restoration</button>
            </div>

            <div class="portfolio-grid">
                <!-- Project Card 1 -->
                <div class="project-card" data-category="design">
                    <div class="card-image"><img src="assets/images/port1.jpg" alt="Modern Family Home"></div>
                    <div class="card-content"><span class="category">Interior Design</span><h4>Modern Family Home</h4><p>A complete overhaul focusing on open spaces and natural light to create a serene family environment.</p></div>
                </div>
                <!-- Project Card 2 -->
                <div class="project-card" data-category="design">
                    <div class="card-image"><img src="assets/images/port2.jpg" alt="Commercial Office Space"></div>
                    <div class="card-content"><span class="category">Interior Design</span><h4>Startup Office Refresh</h4><p>Designed a vibrant, collaborative workspace for a tech startup, incorporating brand colors and flexible furniture.</p></div>
                </div>
                <!-- Project Card 3 -->
                <div class="project-card" data-category="painting">
                    <div class="card-image"><img src="assets/images/port3.jpg" alt="Cozy Bedroom Repaint"></div>
                    <div class="card-content"><span class="category">Painting</span><h4>Cozy Bedroom Repaint</h4><p>Utilized a warm, neutral color palette to transform a master bedroom into a tranquil, relaxing retreat.</p></div>
                </div>
                <!-- Project Card 4 -->
                <div class="project-card" data-category="restoration">
                    <div class="card-image"><img src="assets/images/port4.jpeg" alt="Antique Furniture Restoration"></div>
                    <div class="card-content"><span class="category">Restoration</span><h4>Heirloom Dresser</h4><p>Carefully restored a 19th-century wooden dresser, preserving its original character while ensuring structural integrity.</p></div>
                </div>
                <!-- Project Card 5 -->
                <div class="project-card" data-category="design">
                    <div class="card-image"><img src="assets/images/port5.jpg" alt="Kitchen Renovation"></div>
                    <div class="card-content"><span class="category">Interior Design</span><h4>Contemporary Kitchen</h4><p>A full kitchen remodel featuring custom cabinetry, quartz countertops, and smart appliances for a stylish culinary space.</p></div>
                </div>
                <!-- Project Card 6 -->
                <div class="project-card" data-category="restoration">
                    <div class="card-image"><img src="assets/images/port6.jpg" alt="Historic Building Facade"></div>
                    <div class="card-content"><span class="category">Restoration</span><h4>Historic Facade</h4><p>Managed the exterior restoration of a historic building, including brickwork repair and repainting to match its original appearance.</p></div>
                </div>
                <!-- Project Card 7 -->
                <div class="project-card" data-category="design">
                    <div class="card-image"><img src="assets/images/port7.jpg" alt="Modern Bathroom Design"></div>
                    <div class="card-content"><span class="category">Interior Design</span><h4>Luxury Bathroom</h4><p>A spa-inspired bathroom design featuring a freestanding tub, marble tiling, and custom vanity for a touch of luxury.</p></div>
                </div>
                <!-- Project Card 8 -->
                <div class="project-card" data-category="painting">
                    <div class="card-image"><img src="assets/images/port8.jpg" alt="Exterior Wall Painting"></div>
                    <div class="card-content"><span class="category">Painting</span><h4>Exterior Facade Update</h4><p>Applied a modern color scheme and weather-resistant paint to a home's exterior, boosting its curb appeal and protection.</p></div>
                </div>
                <!-- Project Card 9 -->
                <div class="project-card" data-category="restoration">
                    <div class="card-image"><img src="assets/images/port9.jpg" alt="Wooden Floor Restoration"></div>
                    <div class="card-content"><span class="category">Restoration</span><h4>Hardwood Floor Refinish</h4><p>Sanded, stained, and sealed original hardwood floors to remove decades of wear and tear, revealing their natural beauty.</p></div>
                </div>
            </div>
        </div>
    </section>

 <!-- =========================================
     BEFORE & AFTER SHOWCASE SECTION
     ========================================= -->
<section class="before-after-section page-section">
    <div class="container">
        <h2 class="section-title">See the Transformation</h2>
        <p class="section-subtitle">Our restoration work speaks for itself. Drag the slider to see the difference.</p>
        <div class="ba-slider">
            <img src="https://images.unsplash.com/photo-1567016376408-0226e4d0c1ea?q=80&w=1374" alt="Living room with old, worn-out furniture before restoration">
            <div class="resize">
                <img src="https://images.unsplash.com/photo-1618220179428-22790b461013?q=80&w=1527" alt="Same living room after restoration with modern, stylish furniture">
            </div>
            <span class="handle"></span>
        </div>
    </div>
</section>

    <!-- =========================================
         START YOUR PROJECT (CTA) SECTION
         ========================================= -->
    <section class="cta-section portfolio-cta">
        <div class="container">
            <h2>Inspired by Our Work?</h2>
            <p>Let's turn your vision into reality. Whether it's a small refresh or a complete renovation, our team is ready to help. Get a transparent quote today.</p>
            <!-- THIS IS THE DYNAMIC LINK -->
            <a href="<?php echo isUserLoggedIn() ? 'services.php' : 'login.php'; ?>" class="btn btn-primary">Start Your Project</a>
        </div>
    </section>

</main>

<?php 
// Include the master footer
include 'footer.php'; 
?>