<?php
    $pageTitle = 'Sign Up'; 
    require_once __DIR__ . '/../config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Innovista</title>
    
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="signup-page-wrapper">
        <div class="signup-container">
            <div class="signup-form-side">
                <a href="index.php" class="home-link"><i class="fas fa-arrow-left"></i> Back to Home</a>
                <h2 class="form-title">Create Your Account</h2>

                <!-- Container for server messages -->
                <div class="flash-message-container">
                     <?php display_flash_message(); ?>
                </div>
                
                <form id="signupForm" method="POST" action="../handlers/handle_signup.php" autocomplete="off" enctype="multipart/form-data">
                    <div class="user-type-group">
                        <button type="button" class="user-type-btn active" data-type="customer">I'm a Customer</button>
                        <button type="button" class="user-type-btn" data-type="provider">I'm a Provider</button>
                        <input type="hidden" id="userType" name="userType" value="customer">
                    </div>

                    <!-- Customer Fields -->
                    <div id="customerFields" class="form-fields active">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="you@example.com" required>
                        </div>
                    </div>

                    <!-- Provider Fields -->
                    <div id="providerFields" class="form-fields">
                         <div class="form-group">
                            <label for="providerFullname">Full Name / Company Name</label>
                            <input type="text" id="providerFullname" name="providerFullname" placeholder="Your professional name">
                        </div>
                        <div class="form-group">
                            <label for="providerEmail">Business Email</label>
                            <input type="email" id="providerEmail" name="providerEmail" placeholder="contact@yourbusiness.com">
                        </div>
                           <div class="form-group">
                               <label for="provider_bio">Bio / Description</label>
                               <textarea id="provider_bio" name="provider_bio" rows="4" placeholder="Describe your business, experience, or specialties"></textarea>
                           </div>
                        <div class="form-group">
                            <label for="providerPhone">Phone Number</label>
                            <input type="tel" id="providerPhone" name="providerPhone" placeholder="Enter phone number" pattern="^[0-9]{10}$" maxlength="10">
                            <small style="color: #888;">Must be 10 digits.</small>
                        </div>
                        <div class="form-group">
                            <label for="providerAddress">Address</label>
                            <input type="text" id="providerAddress" name="providerAddress" placeholder="Enter address">
                        </div>
                        
                        <div class="form-group">
                            <label for="providerCV">Upload Portfolio (PDF, DOC, JPG, PNG)</label>
                            <input type="file" id="providerCV" name="providerCV" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        </div>
                        <div class="form-group">
                            <label for="providerService">Service</label>
                            <select id="providerService" name="providerService[]" multiple>
                                <option value="Interior Design">Interior Design</option>
                                <option value="Painting">Painting</option>
                                <option value="Restoration">Restoration</option>
                            </select>
                        </div>
                        <div class="form-group" id="providerSubcategories" style="display:none;">
                            <label>Subcategories (select all that apply):</label>
                            <div id="subcategoryCheckboxes"></div>
                        </div>
                    </div>
                    
                    <!-- Common Fields -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                    </div>
                     <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary signup-btn">Create Account</button>
                    
                    <p class="terms-text">
                        By creating an account, you agree to our <a href="#">Terms of Service</a>.
                    </p>
                </form>
            </div>
            
            <div class="signup-welcome-side">
                <div class="welcome-overlay">
                    <h1 class="welcome-title">Join Innovista</h1>
                    <p class="welcome-subtitle">The #1 platform for connecting clients with trusted design and restoration professionals.</p>
                    <div class="welcome-login-link">
                        Already have an account? <a href="./login.php">Log in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This script is for the customer/provider toggle button -->
    <script>
    // Phone number validation
    document.getElementById('signupForm').addEventListener('submit', function(e) {
        var phoneInput = document.getElementById('providerPhone');
        if (document.getElementById('userType').value === 'provider') {
            var phone = phoneInput.value.trim();
            if (!/^\d{10}$/.test(phone)) {
                alert('Phone number must be exactly 10 digits.');
                phoneInput.focus();
                e.preventDefault();
                return false;
            }
        }
    });

    // Subcategory options for each main service
    const subcategories = {
        "Interior Design": [
            "Ceiling & Lighting",
            "Space Planning",
            "Modular Kitchen",
            "Bathroom Design",
            "Carpentry & Woodwork",
            "Furniture Design"
        ],
        "Painting": [
            "Interior Painting",
            "Exterior Painting",
            "Water & Damp Proofing",
            "Commercial Painting",
            "Wall Art & Murals",
            "Color Consultation"
        ],
        "Restoration": [
            "Wall Repairs & Plastering",
            "Floor Restoration",
            "Door & Window Repairs",
            "Old Space Transformation",
            "Furniture Restoration",
            "Full Building Renovation"
        ]
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Toggle customer/provider fields and required attributes
        const userTypeButtons = document.querySelectorAll('.user-type-btn');
        const userTypeInput = document.getElementById('userType');
        const customerFields = document.getElementById('customerFields');
        const providerFields = document.getElementById('providerFields');
        const customerName = document.getElementById('name');
        const customerEmail = document.getElementById('email');
        const providerName = document.getElementById('providerFullname');
        const providerEmail = document.getElementById('providerEmail');
    // providerService already declared above

        userTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                userTypeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const type = this.getAttribute('data-type');
                userTypeInput.value = type;
                if (type === 'customer') {
                    customerFields.classList.add('active');
                    providerFields.classList.remove('active');
                    // Set required for customer fields
                    customerName.required = true;
                    customerEmail.required = true;
                    // Remove required from provider fields
                    providerName.required = false;
                    providerEmail.required = false;
                    providerService.required = false;
                } else {
                    providerFields.classList.add('active');
                    customerFields.classList.remove('active');
                    // Set required for provider fields
                    providerName.required = true;
                    providerEmail.required = true;
                    providerService.required = true;
                    // Remove required from customer fields
                    customerName.required = false;
                    customerEmail.required = false;
                }
            });
        });

        // Provider subcategory logic
        const providerService = document.getElementById('providerService');
        const subcatContainer = document.getElementById('providerSubcategories');
        const subcatCheckboxes = document.getElementById('subcategoryCheckboxes');

        providerService.addEventListener('change', function() {
            const selected = Array.from(this.selectedOptions).map(opt => opt.value);
            subcatCheckboxes.innerHTML = '';
            if (selected.length > 0) {
                subcatContainer.style.display = 'block';
                selected.forEach(function(service) {
                    if (subcategories[service]) {
                        const label = document.createElement('div');
                        label.style.fontWeight = 'bold';
                        label.textContent = service + ' Subcategories:';
                        subcatCheckboxes.appendChild(label);
                        subcategories[service].forEach(function(subcat) {
                            const id = 'subcat_' + service.replace(/\s+/g, '_') + '_' + subcat.replace(/\s+/g, '_');
                            const checkboxLabel = document.createElement('label');
                            checkboxLabel.style.display = 'inline-block';
                            checkboxLabel.style.marginRight = '10px';
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'providerSubcategories[]';
                            checkbox.value = service + ' - ' + subcat;
                            checkbox.id = id;
                            checkboxLabel.appendChild(checkbox);
                            checkboxLabel.appendChild(document.createTextNode(' ' + subcat));
                            subcatCheckboxes.appendChild(checkboxLabel);
                        });
                    }
                });
            } else {
                subcatContainer.style.display = 'none';
                subcatCheckboxes.innerHTML = '';
            }
        });
    });
    </script>
</body>
</html>