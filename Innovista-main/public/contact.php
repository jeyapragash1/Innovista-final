<?php
// Define the page title for this specific page
$pageTitle = 'Contact Us'; 
// Include the master header
include 'header.php'; 
?>

<!-- =========================================
     CONTACT HERO HEADER
     ========================================= -->
<section class="contact-hero">
    <div class="container">
        <h1>Get in Touch</h1>
        <p>We're here to help! Whether you have a question about our services or need support, please reach out.</p>
    </div>
</section>

<!-- =========================================
     MAIN CONTACT CONTENT
     ========================================= -->
<main class="contact-main-content page-section">
    <div class="container">
        <div class="contact-layout">
            <!-- Left Side: Contact Form -->
            <div class="contact-form-wrapper">
                <h2 class="form-title">Send Us a Message</h2>
                <form id="contactForm" novalidate>
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        <div class="error" id="nameError"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" required>
                        <div class="error" id="emailError"></div>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="e.g., Question about Interior Design" required>
                        <div class="error" id="subjectError"></div>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Enter your message here..." required></textarea>
                        <div class="error" id="messageError"></div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">Send Message</button>
                </form>
            </div>

            <!-- Right Side: Contact Information -->
            <div class="contact-info-wrapper">
                <h2 class="info-title">Contact Information</h2>
                <p class="info-intro">Feel free to contact us through any of the following methods. We look forward to hearing from you.</p>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Our Office</h3>
                        <p>25, KKS Road, Jaffna, Sri Lanka</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h3>Phone</h3>
                        <p>(+94) 77 442 2448</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h3>Email</h3>
                        <p>info@innovista.com</p>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Business Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Google Maps Section -->
        <div class="map-section">
            <iframe 
                src="https://www.google.com/maps?q=Jaffna+Northern+Province+Sri+Lanka&output=embed" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</main>

<?php 
// Include the master footer
include 'footer.php'; 
?>