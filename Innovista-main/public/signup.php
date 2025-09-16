<?php
    $pageTitle = 'Sign Up'; 
    require_once __DIR__ . '/../config/session.php';
    // No need for header.php here as it's a standalone page, but session is needed.

    // If a user is already logged in, redirect them away from signup
    if (isUserLoggedIn()) {
        $userRole = getUserRole();
        if ($userRole === 'admin') {
            header("Location: ../admin/admin_dashboard.php");
        } elseif ($userRole === 'provider') {
            header("Location: provider_dashboard.php");
        } else { // customer or unknown
            header("Location: customer_dashboard.php");
        }
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> - Innovista</title>
    
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
                     <?php // This function should be defined in a utils/flash_message.php or similar ?>
                     <?php // Assuming display_flash_message() exists and handles output ?>
                     <?php if (function_exists('display_flash_message')) display_flash_message(); ?>
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
                            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?php echo htmlspecialchars($_SESSION['signup_data']['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="you@example.com" value="<?php echo htmlspecialchars($_SESSION['signup_data']['email'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="customerPhone">Phone Number</label>
                            <input type="tel" id="customerPhone" name="customerPhone" placeholder="Enter phone number" pattern="^[0-9]{10,15}$" maxlength="15" value="<?php echo htmlspecialchars($_SESSION['signup_data']['customerPhone'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="customerAddress">Address</label>
                            <input type="text" id="customerAddress" name="customerAddress" placeholder="Enter address" value="<?php echo htmlspecialchars($_SESSION['signup_data']['customerAddress'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- Provider Fields -->
                    <div id="providerFields" class="form-fields">
                         <div class="form-group">
                            <label for="providerFullname">Full Name / Company Name</label>
                            <input type="text" id="providerFullname" name="providerFullname" placeholder="Your professional name" value="<?php echo htmlspecialchars($_SESSION['signup_data']['providerFullname'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="providerEmail">Business Email</label>
                            <input type="email" id="providerEmail" name="providerEmail" placeholder="contact@yourbusiness.com" value="<?php echo htmlspecialchars($_SESSION['signup_data']['providerEmail'] ?? ''); ?>">
                        </div>
                           <div class="form-group">
                               <label for="provider_bio">Bio / Description</label>
                               <textarea id="provider_bio" name="provider_bio" rows="4" placeholder="Describe your business, experience, or specialties"><?php echo htmlspecialchars($_SESSION['signup_data']['provider_bio'] ?? ''); ?></textarea>
                           </div>
                        <div class="form-group">
                            <label for="providerPhone">Phone Number</label>
                            <input type="tel" id="providerPhone" name="providerPhone" placeholder="Enter phone number" pattern="^[0-9]{10}$" maxlength="10" value="<?php echo htmlspecialchars($_SESSION['signup_data']['providerPhone'] ?? ''); ?>">
                            <small style="color: #888;">Must be 10 digits.</small>
                        </div>
                        <div class="form-group">
                            <label for="providerAddress">Address</label>
                            <input type="text" id="providerAddress" name="providerAddress" placeholder="Enter address" value="<?php echo htmlspecialchars($_SESSION['signup_data']['providerAddress'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="providerCV">Upload Portfolio/Credentials (PDF, DOC, JPG, PNG) (Optional)</label>
                            <input type="file" id="providerCV" name="providerCV" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <small style="color: #888;">This is optional during signup, can be added later.</small>
                        </div>
                        <div class="form-group">
                            <label for="providerService">Main Service(s) (select all that apply)</label>
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
    document.addEventListener('DOMContentLoaded', function() {
        const subcategories = {
            "Interior Design": [
                "Ceiling & Lighting", "Space Planning", "Modular Kitchen", 
                "Bathroom Design", "Carpentry & Woodwork", "Furniture Design"
            ],
            "Painting": [
                "Interior Painting", "Exterior Painting", "Water & Damp Proofing", 
                "Commercial Painting", "Wall Art & Murals", "Color Consultation"
            ],
            "Restoration": [
                "Wall Repairs & Plastering", "Floor Restoration", "Door & Window Repairs", 
                "Old Space Transformation", "Furniture Restoration", "Full Building Renovation"
            ]
        };

        const userTypeButtons = document.querySelectorAll('.user-type-btn');
        const userTypeInput = document.getElementById('userType');
        const customerFields = document.getElementById('customerFields');
        const providerFields = document.getElementById('providerFields');

        // Customer fields
        const customerName = document.getElementById('name');
        const customerEmail = document.getElementById('email');
        const customerPhone = document.getElementById('customerPhone');
        const customerAddress = document.getElementById('customerAddress');

        // Provider fields
        const providerFullname = document.getElementById('providerFullname');
        const providerEmail = document.getElementById('providerEmail');
        const providerBio = document.getElementById('provider_bio');
        const providerPhone = document.getElementById('providerPhone');
        const providerAddress = document.getElementById('providerAddress');
        const providerCV = document.getElementById('providerCV');
        const providerService = document.getElementById('providerService');
        const subcatContainer = document.getElementById('providerSubcategories');
        const subcatCheckboxes = document.getElementById('subcategoryCheckboxes');

        // Common fields
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');


        function setRequiredAttributes(type) {
            // Reset all required attributes first
            [customerName, customerEmail, customerPhone, customerAddress, 
             providerFullname, providerEmail, providerBio, providerPhone, providerAddress, 
             providerCV, providerService].forEach(field => {
                if (field) field.removeAttribute('required');
            });
            Array.from(subcatCheckboxes.querySelectorAll('input[type="checkbox"]')).forEach(cb => cb.removeAttribute('required'));

            // Set common required fields
            password.required = true;
            confirmPassword.required = true;

            if (type === 'customer') {
                customerName.required = true;
                customerEmail.required = true;
                // customerPhone and customerAddress are optional
            } else { // provider
                providerFullname.required = true;
                providerEmail.required = true;
                providerPhone.required = true;
                providerService.required = true;
                // providerBio, providerAddress, providerCV are optional
            }
        }

        function toggleUserTypeFields(type) {
            if (type === 'customer') {
                customerFields.classList.add('active');
                providerFields.classList.remove('active');
            } else { // provider
                providerFields.classList.add('active');
                customerFields.classList.remove('active');
                // Manually trigger change for providerService to load subcategories if any are pre-selected
                const event = new Event('change');
                providerService.dispatchEvent(event);
            }
            setRequiredAttributes(type);
        }

        userTypeButtons.forEach(button => {
            button.addEventListener('click', function() {
                userTypeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const type = this.getAttribute('data-type');
                userTypeInput.value = type;
                toggleUserTypeFields(type);
            });
        });

        providerService.addEventListener('change', function() {
            const selected = Array.from(this.selectedOptions).map(opt => opt.value);
            subcatCheckboxes.innerHTML = ''; // Clear previous checkboxes

            if (selected.length > 0) {
                subcatContainer.style.display = 'block';
                selected.forEach(function(service) {
                    if (subcategories[service]) {
                        const label = document.createElement('div');
                        label.style.fontWeight = 'bold';
                        label.style.marginTop = '10px';
                        label.textContent = service + ' Subcategories:';
                        subcatCheckboxes.appendChild(label);
                        subcategories[service].forEach(function(subcat) {
                            const id = 'subcat_' + service.replace(/\s+/g, '_') + '_' + subcat.replace(/\s+/g, '_');
                            const checkboxLabel = document.createElement('label');
                            checkboxLabel.className = 'checkbox-label'; // Add class for styling
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'providerSubcategories[]';
                            checkbox.value = service + ' - ' + subcat;
                            checkbox.id = id;
                            // Pre-select if previously submitted (for error re-display)
                            const prevSubcategories = <?php echo json_encode($_SESSION['signup_data']['providerSubcategories'] ?? []); ?>;
                            if (prevSubcategories.includes(service + ' - ' + subcat)) {
                                checkbox.checked = true;
                            }

                            checkboxLabel.appendChild(checkbox);
                            checkboxLabel.appendChild(document.createTextNode(' ' + subcat));
                            subcatCheckboxes.appendChild(checkboxLabel);
                        });
                    }
                });
            } else {
                subcatContainer.style.display = 'none';
            }
        });

        // Initialize state based on pre-selected user type (e.g., from error re-display)
        const initialUserType = userTypeInput.value;
        toggleUserTypeFields(initialUserType);
        userTypeButtons.forEach(btn => {
            if (btn.getAttribute('data-type') === initialUserType) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Password matching validation
        const signupForm = document.getElementById('signupForm');
        signupForm.addEventListener('submit', function(e) {
            if (password.value !== confirmPassword.value) {
                alert('Passwords do not match!');
                e.preventDefault();
                confirmPassword.focus();
            }
            // Basic phone validation already included, but could be enhanced here.
        });
    });
    </script>
</body>
</html>