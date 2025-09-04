<?php require_once 'admin_header.php'; ?>

<h2>System Settings & Homepage Content</h2>
<p>Manage global settings for the Innovista platform.</p>

<form action="save_settings.php" method="POST">
    <div class="content-card">
        <h3>Homepage Content</h3>
        <div class="form-group">
            <label for="welcome_message">Welcome Message (Main Heading)</label>
            <input type="text" id="welcome_message" name="welcome_message" value="Welcome to Innovista! Your one-stop solution for interior design and restoration services.">
        </div>
        <div class="form-group">
            <label for="about_text">About Us Text</label>
            <textarea id="about_text" name="about_text" rows="4">Imovista is a premier platform connecting skilled interior designers and restoration experts with clients seeking quality and reliability.</textarea>
        </div>
    </div>

    <div class="content-card">
        <h3>Platform Information</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="platform_name">Platform Name</label>
                <input type="text" id="platform_name" name="platform_name" value="Innovista">
            </div>
             <div class="form-group">
                <label for="admin_email">Admin Contact Email</label>
                <input type="email" id="admin_email" name="admin_email" value="contact@innovista.com">
            </div>
        </div>
        <div class="form-group">
            <label for="platform_address">Company Address</label>
            <input type="text" id="platform_address" name="platform_address" value="123 Design Lane, Jaffna, Sri Lanka">
        </div>
    </div>

    <div class="content-card">
        <h3>Social Media Links</h3>
        <div class="form-grid">
            <div class="form-group">
                <label for="facebook_url">Facebook URL</label>
                <input type="url" id="facebook_url" name="facebook_url" placeholder="https://facebook.com/innovista">
            </div>
             <div class="form-group">
                <label for="instagram_url">Instagram URL</label>
                <input type="url" id="instagram_url" name="instagram_url" placeholder="https://instagram.com/innovista">
            </div>
        </div>
    </div>

    <button type="submit" class="btn-save">Save All Settings</button>
</form>

<?php require_once 'admin_footer.php'; ?>