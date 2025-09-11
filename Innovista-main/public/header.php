<?php
    // C:\xampp1\htdocs\Innovista-final\Innovista-main\public\header.php

    // Include the session manager. This file now defines our helper functions.
    include_once 'session.php'; 

    // --- Removed the helper function definitions from here ---
    // They are now in session.php

    // Get the current page's filename for the active navigation link.
    $currentPage = basename($_SERVER['SCRIPT_NAME']);

    // --- DYNAMIC DASHBOARD URL LOGIC ---
    // This logic determines where a logged-in user's dashboard link should point.
    $dashboardUrl = 'login.php'; // Default URL if not logged in or role not recognized

    if (isUserLoggedIn()) { // This function is now available from session.php
        switch (getUserRole()) { // This function is now available from session.php
            case 'admin':
                $dashboardUrl = '../admin/admin_dashboard.php';
                break;
            case 'provider':
                $dashboardUrl = 'provider_dashboard.php'; // Assuming this is in public/
                break;
            case 'customer':
                $dashboardUrl = 'customer_dashboard.php'; // Assuming this is in public/
                break;
            default:
                $dashboardUrl = 'index.php'; // Fallback for logged-in but unrecognized role
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Use $pageTitle if set, otherwise a default title -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - Innovista' : 'Innovista - Transforming Spaces'; ?></title>
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Your Project Stylesheets -->
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
<!-- Add data-user-role for potential JavaScript interactions -->
<body data-logged-in="<?php echo isUserLoggedIn() ? 'true' : 'false'; ?>" data-user-role="<?php echo htmlspecialchars(getUserRole() ?? 'guest'); ?>">
<header class="main-header">
    <nav class="navbar container">
        <a href="./index.php" class="navbar-logo">
            <img src="assets/images/logo1.png" alt="Innovista Logo">
            <span>INNOVISTA</span>
        </a>
        <ul class="navbar-menu">
            <li class="<?php if ($currentPage == 'index.php') {echo 'active';} ?>"><a href="./index.php">Home</a></li>
            <li class="<?php if ($currentPage == 'services.php') {echo 'active';} ?>"><a href="./services.php">Services</a></li>
            <li class="<?php if ($currentPage == 'product.php') {echo 'active';} ?>"><a href="./product.php">Products</a></li> 
            <li class="<?php if ($currentPage == 'portfolio.php') {echo 'active';} ?>"><a href="./portfolio.php">Portfolio</a></li>             
        </ul>

        <div class="navbar-actions">
            <?php if (isUserLoggedIn()): ?>
                <!-- User Profile Icon with Dropdown -->
                <div class="user-profile">
                    <!-- Dashboard link in the main nav action area -->
                    <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="user-icon-link">
                        <i class="fas fa-user-circle"></i>
                    </a>
                    <div class="profile-dropdown">
                        <!-- Dashboard link in the dropdown -->
                        <a href="<?php echo htmlspecialchars($dashboardUrl); ?>" class="dropdown-item">My Dashboard</a>
                        <a href="profile.php" class="dropdown-item">Edit Profile</a>
                        <a href="logout.php" class="dropdown-item logout">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Show Login and Sign Up if user IS NOT logged in -->
                <a href="login.php" class="btn-earthy">Login</a>
                <a href="signup.php" class="btn-earthy">Sign Up</a>
            <?php endif; ?>
        </div>
        
        <button class="navbar-toggle" id="navbar-toggle" aria-label="Menu">
            <span class="navbar-toggle-icon"></span>
        </button>
    </nav>
</header>