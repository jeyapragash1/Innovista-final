<?php
    // C:\xampp1\htdocs\Innovista-final\Innovista-main\public\header.php

    // Include the session manager. This file now defines our helper functions:
    // isUserLoggedIn(), getUserRole(), getUserId().
    include_once 'session.php'; 

    // Get the current page's filename for dynamic 'active' navigation link styling.
    $currentPage = basename($_SERVER['SCRIPT_NAME']);

    // --- DYNAMIC DASHBOARD URL LOGIC ---
    // This logic determines the correct dashboard URL based on the logged-in user's role.
    // Default to the login page if the user is not logged in or their role is unknown.
    $dashboardUrl = 'login.php'; 

    if (isUserLoggedIn()) {
                switch (getUserRole()) {
            case 'admin':
                $dashboardUrl = '../admin/admin_dashboard.php';
                break;
            case 'provider':
                // Corrected path: from public/ to provider/
                $dashboardUrl = '../provider/provider_dashboard.php';
                break;
            case 'customer':
                // Corrected path: from public/ to customer/
                $dashboardUrl = '../customer/customer_dashboard.php';
                break;
            default:
                $dashboardUrl = 'index.php';
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Sets the page title dynamically. $pageTitle is expected to be set in the including PHP file (e.g., index.php). -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Innovista' : 'Innovista - Transforming Spaces'; ?></title>
    
    <!-- External Libraries (Font Awesome, Google Fonts) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Your Project Specific Stylesheets -->
    <!-- Ensure these paths are correct relative to the 'public' directory -->
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/services.css"> 
    <link rel="stylesheet" href="assets/css/product.css">
    <link rel="stylesheet" href="assets/css/portfolio.css">
    <link rel="stylesheet" href="assets/css/contact.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="assets/css/serviceprovider.css">

    
</head>
<!-- Body tag with data attributes for JavaScript to easily check login status and user role -->
<body data-logged-in="<?php echo isUserLoggedIn() ? 'true' : 'false'; ?>" data-user-role="<?php echo htmlspecialchars(getUserRole() ?? 'guest'); ?>">
<header class="main-header">
    <nav class="navbar container">
        <!-- Logo and Site Title, links to the homepage -->
        <a href="./index.php" class="navbar-logo">
            <img src="assets/images/logo1.png" alt="Innovista Logo">
            <span>INNOVISTA</span>
        </a>
        <!-- Main Navigation Menu -->
        <ul class="navbar-menu">
            <li class="<?php if ($currentPage == 'index.php') {echo 'active';} ?>"><a href="./index.php">Home</a></li>
            <li class="<?php if ($currentPage == 'services.php') {echo 'active';} ?>"><a href="./services.php">Services</a></li>
            <li class="<?php if ($currentPage == 'product.php') {echo 'active';} ?>"><a href="./product.php">Products</a></li> 
            <li class="<?php if ($currentPage == 'portfolio.php') {echo 'active';} ?>"><a href="./portfolio.php">Portfolio</a></li>             
        </ul>

        <!-- User Actions (Login/Signup or Profile Dropdown) -->
        <div class="navbar-actions">
            <?php if (isUserLoggedIn()): ?>
                <!-- If user is logged in, show profile icon and dropdown menu -->
                <div class="user-profile">
                    <!-- Link for the user icon, directs to their respective dashboard -->
                    <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="user-icon-link">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <div class="profile-dropdown">
                        <!-- "My Dashboard" link in the dropdown, also directs to their dashboard -->
                        <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="dropdown-item">My Dashboard</a>
                        
                        <?php if (getUserRole() !== 'admin'): ?>
                            <!-- "Edit Profile" link, only visible for non-admin users in the public site -->
                            <!-- Admins typically manage their core details via the admin panel's System Settings -->
                            <a href="profile.php" class="dropdown-item">Edit Profile</a>
                        <?php endif; ?>
                        
                        <!-- Logout link -->
                        <a href="logout.php" class="dropdown-item logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- If user is NOT logged in, show Login and Sign Up buttons -->
                <a href="login.php" class="btn-earthy">Login</a>
                <a href="signup.php" class="btn-earthy">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <!-- Navbar Toggle button for mobile responsiveness -->
        <button class="navbar-toggle" id="navbar-toggle" aria-label="Menu">
            <span class="navbar-toggle-icon"></span>
        </button>
    </nav>
</header>