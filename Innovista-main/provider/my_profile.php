<?php
require_once '../config/session.php';
protectPage('provider');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'My Profile';
require_once '../provider/provider_header.php';
require_once '../config/Database.php';
$provider_id = $_SESSION['user_id'] ?? 0;
$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT * FROM service WHERE provider_id = :provider_id LIMIT 1');
$stmt->bindParam(':provider_id', $provider_id);
$stmt->execute();
$provider = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$provider) {
    $provider = [
        'provider_name' => '',
        'provider_email' => '',
        'provider_phone' => '',
        'provider_address' => '',
        'provider_bio' => '',
        'main_service' => '',
        'subcategories' => '',
        'portfolio' => ''
    ];
}
?>


<!-- Flash message for profile update -->



<div class="profile-page-wrapper">
    <h2 style="margin-bottom: 0.5rem;">Business Profile</h2>
    <p style="margin-bottom: 2rem; color: #666;">Keep your information up to date to attract more clients.</p>


    <form action="update_profile.php" method="POST" class="profile-form content-card business-info-form" style="margin-bottom: 2.5rem;">
        <h3 class="section-title">Business Information</h3>
        <div class="form-grid-pro" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem 2.5rem; align-items: start;">
            <div class="form-group-pro">
                <label for="company_name">Full Name / Company Name <span class="required">*</span></label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($provider['provider_name'] ?? ''); ?>" required autocomplete="organization" placeholder="e.g. Daniel Company or John Doe">
            </div>
            <div class="form-group-pro">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($provider['provider_email'] ?? ''); ?>" required autocomplete="email" placeholder="e.g. daniel@email.com">
            </div>
            <div class="form-group-pro">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($provider['provider_phone'] ?? ''); ?>" autocomplete="tel" placeholder="e.g. 07XXXXXXXX">
            </div>
            <div class="form-group-pro">
                <label for="provider_address">Address</label>
                <textarea id="provider_address" name="provider_address" rows="2" placeholder="e.g. 123 Main St, City, Country"><?php echo htmlspecialchars($provider['provider_address'] ?? ''); ?></textarea>
            </div>
            <div class="form-group-pro" style="grid-column: 1 / -1;">
                <label for="provider_bio">Bio / Description</label>
                <textarea id="provider_bio" name="provider_bio" rows="4" placeholder="Describe your business, experience, or specialties..." style="min-height: 80px;"><?php echo htmlspecialchars($provider['provider_bio'] ?? ''); ?></textarea>
            </div>
        </div>
        <button type="submit" name="update_details" class="btn-submit-pro">Save Business Info</button>
    </form>

    <style>
    .profile-page-wrapper {
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1.5rem 2rem 1.5rem;
        box-sizing: border-box;
    }
    .business-info-form {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
        margin-bottom: 2.5rem;
    }
    .section-title {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 1.7rem;
        color: #18181b;
        letter-spacing: 0.2px;
        border-bottom: 1.5px solid #ececec;
        padding-bottom: 0.7rem;
    }
    .form-grid-pro {
        margin-bottom: 1.5rem;
    }
    .form-group-pro {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .form-group-pro label {
        font-weight: 600;
        color: #22223b;
        font-size: 1.08rem;
        margin-bottom: 0.2rem;
    }
    .form-group-pro input,
    .form-group-pro textarea {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        font-size: 1.05rem;
        background: #f8fafc;
        transition: border 0.2s;
        outline: none;
    }
    .form-group-pro input:focus,
    .form-group-pro textarea:focus {
        border-color: #0d9488;
        background: #fff;
    }
    .form-group-pro textarea {
        resize: vertical;
        min-height: 48px;
        max-height: 180px;
    }
    .btn-submit-pro {
        background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.08rem;
        border: none;
        border-radius: 8px;
        padding: 0.85rem 2.2rem;
        margin-top: 1.2rem;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(13,148,136,0.08);
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-submit-pro:hover {
        background: linear-gradient(90deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 16px rgba(13,148,136,0.13);
    }
    .required {
        color: #e11d48;
        font-size: 1.1em;
        margin-left: 2px;
    }
    @media (max-width: 1100px) {
        .profile-page-wrapper {
            max-width: 98vw;
            padding: 1rem 0.5rem 1rem 0.5rem;
        }
    }
    @media (max-width: 700px) {
        .form-grid-pro {
            grid-template-columns: 1fr !important;
            gap: 1.2rem 0 !important;
        }
        .business-info-form {
            padding: 1.2rem 0.7rem 1.2rem 0.7rem;
        }
        .profile-page-wrapper {
            max-width: 100vw;
            padding: 0.5rem 0.2rem 0.5rem 0.2rem;
        }
    }
    </style>


    <div class="services-section-pro">
        <h3 class="services-title-pro">My Services</h3>
        <p class="services-description-pro">Select the services you offer to help customers find you easily.</p>
        
        <form action="update_profile.php" method="POST" class="services-form-pro">
            <div class="services-main-pro">
                <label for="providerService" class="services-label-pro">Main Services</label>
                <div class="service-options-pro">
                    <label class="service-option-pro">
                        <input type="checkbox" name="providerService[]" value="Interior Design" <?php echo (strpos($provider['main_service'], 'Interior Design') !== false) ? 'checked' : ''; ?>>
                        <span class="service-icon-pro"><i class="fas fa-couch"></i></span>
                        <span class="service-text-pro">Interior Design</span>
                    </label>
                    <label class="service-option-pro">
                        <input type="checkbox" name="providerService[]" value="Painting" <?php echo (strpos($provider['main_service'], 'Painting') !== false) ? 'checked' : ''; ?>>
                        <span class="service-icon-pro"><i class="fas fa-paint-roller"></i></span>
                        <span class="service-text-pro">Painting</span>
                    </label>
                    <label class="service-option-pro">
                        <input type="checkbox" name="providerService[]" value="Restoration" <?php echo (strpos($provider['main_service'], 'Restoration') !== false) ? 'checked' : ''; ?>>
                        <span class="service-icon-pro"><i class="fas fa-hammer"></i></span>
                        <span class="service-text-pro">Restoration</span>
                    </label>
                </div>
            </div>
            
            <div class="subcategories-section-pro" id="providerSubcategories" style="display:none;">
                <label class="subcategories-label-pro">Specializations</label>
                <p class="subcategories-hint-pro">Select all specializations that apply to your services</p>
                <div id="subcategoryCheckboxes" class="subcategory-grid-pro"></div>
            </div>
            
            <button type="submit" name="update_services" class="btn-save-services-pro">
                <i class="fas fa-save"></i>
                Save Services
            </button>
        </form>
    </div>

    <style>
    /* Services Section Styling */
    .services-section-pro {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
        margin-bottom: 2.5rem;
    }
    
    .services-title-pro {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #18181b;
        letter-spacing: 0.2px;
        border-bottom: 1.5px solid #ececec;
        padding-bottom: 0.7rem;
    }
    
    .services-description-pro {
        color: #666;
        font-size: 1rem;
        margin-bottom: 2rem;
        line-height: 1.5;
    }
    
    .services-form-pro {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .services-main-pro {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .services-label-pro {
        font-weight: 600;
        color: #22223b;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }
    
    .service-options-pro {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .service-option-pro {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        user-select: none;
    }
    
    .service-option-pro:hover {
        background: #f1f5f9;
        border-color: #0d9488;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 148, 136, 0.1);
    }
    
    .service-option-pro input[type="checkbox"] {
        display: none;
    }
    
    .service-option-pro input[type="checkbox"]:checked + .service-icon-pro {
        background: #0d9488;
        color: #fff;
        transform: scale(1.1);
    }
    
    .service-option-pro input[type="checkbox"]:checked ~ .service-text-pro {
        color: #0d9488;
        font-weight: 600;
    }
    
    .service-option-pro input[type="checkbox"]:checked {
        background: #ecfdf5;
        border-color: #0d9488;
    }
    
    .service-icon-pro {
        width: 40px;
        height: 40px;
        background: #e2e8f0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 1.2rem;
        transition: all 0.3s ease;
    }
    
    .service-text-pro {
        font-size: 1rem;
        font-weight: 500;
        color: #374151;
        transition: all 0.3s ease;
    }
    
    .subcategories-section-pro {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }
    
    .subcategories-label-pro {
        font-weight: 600;
        color: #22223b;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .subcategories-hint-pro {
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    
    .subcategory-grid-pro {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .subcategory-grid-pro label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
    }
    
    .subcategory-grid-pro label:hover {
        background: #f9fafb;
        border-color: #0d9488;
    }
    
    .subcategory-grid-pro input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #0d9488;
        cursor: pointer;
    }
    
    .service-category-header {
        font-weight: 700;
        margin: 1rem 0 0.5rem 0;
        color: #0d9488;
        font-size: 1rem;
        grid-column: 1 / -1;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .subcategory-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: #fff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.95rem;
        font-weight: 500;
        color: #374151;
    }
    
    .subcategory-option:hover {
        background: #f9fafb;
        border-color: #0d9488;
    }
    
    .subcategory-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #0d9488;
        cursor: pointer;
    }
    
    .btn-save-services-pro {
        background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        border: none;
        border-radius: 10px;
        padding: 0.875rem 2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(13, 148, 136, 0.2);
        align-self: flex-start;
    }
    
    .btn-save-services-pro:hover {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(13, 148, 136, 0.3);
    }
    
    .btn-save-services-pro i {
        font-size: 1rem;
    }
    
    @media (max-width: 768px) {
        .services-section-pro {
            padding: 1.5rem 1rem;
        }
        
        .service-options-pro {
            grid-template-columns: 1fr;
        }
        
        .subcategory-grid-pro {
            grid-template-columns: 1fr;
        }
        
        .btn-save-services-pro {
            width: 100%;
        }
    }
    </style>

    <script>
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
        const serviceCheckboxes = document.querySelectorAll('input[name="providerService[]"]');
        const subcatContainer = document.getElementById('providerSubcategories');
        const subcatCheckboxes = document.getElementById('subcategoryCheckboxes');
        
        function updateSubcategories() {
            const selected = Array.from(serviceCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            subcatCheckboxes.innerHTML = '';
            
            if (selected.length > 0) {
                subcatContainer.style.display = 'block';
                
                selected.forEach(function(service) {
                    if (subcategories[service]) {
                        // Create section header for this service
                        const serviceHeader = document.createElement('div');
                        serviceHeader.className = 'service-category-header';
                        serviceHeader.textContent = service + ' Specializations:';
                        subcatCheckboxes.appendChild(serviceHeader);
                        
                        // Create checkboxes for each subcategory
                        subcategories[service].forEach(function(subcat) {
                            const id = 'subcat_' + service.replace(/\s+/g, '_') + '_' + subcat.replace(/\s+/g, '_');
                            const checkboxLabel = document.createElement('label');
                            checkboxLabel.className = 'subcategory-option';
                            
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.name = 'providerSubcategories[]';
                            checkbox.value = service + ' - ' + subcat;
                            checkbox.id = id;
                            
                            // Pre-check if already selected
                            <?php if (!empty($provider['subcategories'])): ?>
                            if (<?php echo json_encode(explode(',', $provider['subcategories'])); ?>.includes(service + ' - ' + subcat)) {
                                checkbox.checked = true;
                            }
                            <?php endif; ?>
                            
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
        }
        
        // Add event listeners to all service checkboxes
        serviceCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateSubcategories);
        });
        
        // Initial call to show subcategories if services are already selected
        updateSubcategories();
    });
    </script>

    <!-- Portfolio section removed -->
    </div>


    <form action="../handlers/handle_update_password.php" method="POST" class="content-card password-form-pro">
        <h3 class="password-title-pro">Change Password</h3>
        <div class="password-grid-pro">
            <div class="password-group-pro">
                <label for="current_password">Current Password <span class="required">*</span></label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" placeholder="Enter current password">
            </div>
            <div class="password-group-pro">
                <label for="new_password">New Password <span class="required">*</span></label>
                <input type="password" id="new_password" name="new_password" required autocomplete="new-password" placeholder="Enter new password">
            </div>
            <div class="password-group-pro" style="grid-column: 1 / -1;">
                <label for="confirm_password">Confirm New Password <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" placeholder="Re-enter new password">
            </div>
        </div>
        <button type="submit" name="update_password" class="btn-password-pro">Update Password</button>
    </form>

    <style>
    .password-form-pro {
        max-width: 600px;
        margin: 0 auto 2.5rem auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        padding: 2.5rem 2.5rem 2rem 2.5rem;
    }
    .password-title-pro {
        font-size: 1.35rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #18181b;
        letter-spacing: 0.2px;
        border-bottom: 1.5px solid #ececec;
        padding-bottom: 0.7rem;
    }
    .password-grid-pro {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem 2.5rem;
        margin-bottom: 1.5rem;
    }
    .password-group-pro {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .password-group-pro label {
        font-weight: 600;
        color: #22223b;
        font-size: 1.08rem;
        margin-bottom: 0.2rem;
    }
    .password-group-pro input {
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        font-size: 1.05rem;
        background: #f8fafc;
        transition: border 0.2s;
        outline: none;
    }
    .password-group-pro input:focus {
        border-color: #0d9488;
        background: #fff;
    }
    .btn-password-pro {
        background: linear-gradient(90deg, #0d9488 0%, #14b8a6 100%);
        color: #fff;
        font-weight: 700;
        font-size: 1.08rem;
        border: none;
        border-radius: 8px;
        padding: 0.85rem 2.2rem;
        margin-top: 1.2rem;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(13,148,136,0.08);
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-password-pro:hover {
        background: linear-gradient(90deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 4px 16px rgba(13,148,136,0.13);
    }
    @media (max-width: 700px) {
        .password-form-pro {
            padding: 1.2rem 0.7rem 1.2rem 0.7rem;
        }
        .password-grid-pro {
            grid-template-columns: 1fr !important;
            gap: 1.2rem 0 !important;
        }
    }
    </style>
</div>

<?php require_once '../includes/user_dashboard_footer.php'; ?>



