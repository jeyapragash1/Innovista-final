<?php
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - Innovista Provider' : 'Provider Dashboard - Innovista'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/dashboard.css"> <!-- Link to the new unified CSS -->
</head>
<body class="dashboard-body"> <!-- Add class to body -->
    <div class="dashboard-container">
        <aside class="dashboard-sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="../index.php" class="sidebar-logo">
                    <img src="../public/assets/images/logo1.png" alt="Logo">
                    <span>Innovista</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <a href="./provider_dashboard.php" class="nav-link <?php if($currentPage == 'provider_dashboard.php') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="./manage_quotations.php" class="nav-link <?php if($currentPage == 'manage_quotations.php') echo 'active'; ?>"><i class="fas fa-file-invoice-dollar"></i><span>Quotations</span></a>
                <a href="./my_projects.php" class="nav-link <?php if($currentPage == 'my_projects.php') echo 'active'; ?>"><i class="fas fa-tasks"></i><span>My Projects</span></a>
                <a href="./manage_portfolio.php" class="nav-link <?php if($currentPage == 'manage_portfolio.php') echo 'active'; ?>"><i class="fas fa-images"></i><span>My Portfolio</span></a>
                <a href="./manage_calendar.php" class="nav-link <?php if($currentPage == 'manage_calendar.php') echo 'active'; ?>"><i class="fas fa-calendar-alt"></i><span>My Calendar</span></a>
                <a href="./view_transactions.php" class="nav-link <?php if($currentPage == 'view_transactions.php') echo 'active'; ?>"><i class="fas fa-receipt"></i><span>My Earnings</span></a>
                <a href="./my_profile.php" class="nav-link <?php if($currentPage == 'my_profile.php') echo 'active'; ?>"><i class="fas fa-user-edit"></i><span>My Profile</span></a>
            </nav>
            <div class="sidebar-footer">
                <a href="../public/logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
            </div>
        </aside>
        
        <div class="content-wrapper">
            <header class="main-header-bar">
                <button class="menu-toggle" id="menu-toggle"><i class="fas fa-bars"></i></button>
                <div class="user-profile-widget">
                    <span>Welcome, <?php echo $_SESSION['user_name'] ?? 'Provider'; ?></span>
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>
            <main class="main-content">