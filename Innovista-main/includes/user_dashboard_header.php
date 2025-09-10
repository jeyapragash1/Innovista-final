<?php
    // In a real application, you would have session start and login checks here
    // session_start();
    // if (!isUserLoggedIn()) {
    //     header("Location: ../login.php");
    //     exit();
    // }

    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    // Make sure you start the session in a config file before this runs
    $userRole = $_SESSION['user_role'] ?? 'customer'; // Default to customer for example
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Innovista</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/assets/css/admin.css">
    <link rel="stylesheet" href="../public/assets/css/customer-dashboard.css">
    <link rel="stylesheet" href="../public/assets/css/notifications.css">
    <!-- Add this before closing head tag -->
    <script defer src="../public/assets/js/notifications.js"></script>

</head>
<body>
    <div class="admin-container">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="../index.php" class="sidebar-logo">
                    <!-- This path is now correct because it's relative to the PHP file's location -->
                    <img src="../public/assets/images/logo1.png" alt="Logo">
                    <span>Innovista</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <!-- Links change based on user role -->
                <?php if ($userRole == 'customer'): ?>
                    <a href="customer_dashboard.php" class="nav-link <?php if($currentPage == 'customer_dashboard.php') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    <a href="request_quotation.php" class="nav-link"><i class="fas fa-file-signature"></i><span>New Quote</span></a>
                    <a href="my_projects.php" class="nav-link"><i class="fas fa-tasks"></i><span>My Projects</span></a>
                    <a href="payment_history.php" class="nav-link"><i class="fas fa-receipt"></i><span>Payments</span></a>
                    <a href="my_profile.php" class="nav-link"><i class="fas fa-user-edit"></i><span>My Profile</span></a>
                <?php else: // Service Provider Links ?>
                    <a href="provider_dashboard.php" class="nav-link <?php if($currentPage == 'provider_dashboard.php') echo 'active'; ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                    <a href="manage_quotations.php" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>Quotations</span></a>
                    <a href="my_projects.php" class="nav-link"><i class="fas fa-tasks"></i><span>My Projects</span></a>
                    <a href="manage_portfolio.php" class="nav-link"><i class="fas fa-images"></i><span>My Portfolio</span></a>
                    <a href="manage_calendar.php" class="nav-link"><i class="fas fa-calendar-alt"></i><span>My Calendar</span></a>
                    <a href="my_profile.php" class="nav-link"><i class="fas fa-user-edit"></i><span>My Profile</span></a>
                <?php endif; ?>
            </nav>
            <div class="sidebar-footer">
                <a href="../public/logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </a>
            </div>
        </aside>
        
        <div class="content-wrapper">
            <header class="main-header-bar">
                <div class="header-left">
                    <button class="menu-toggle" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="user-welcome">
                        <span>Welcome, <?php echo $_SESSION['user_name'] ?? 'User'; ?></span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
                <div class="header-right">
                    <div class="notifications-wrapper">
                        <div class="notifications-icon" id="notificationsToggle">
                            <i class="fas fa-bell"></i>
                            <span class="notification-count" id="notificationCount">0</span>
                        </div>
                        <div class="notifications-dropdown" id="notificationsDropdown">
                            <div class="notifications-header">
                                <h3>Notifications</h3>
                                <button class="mark-all-read">Mark all as read</button>
                            </div>
                            <div class="notifications-list" id="notificationsList">
                                <!-- Notifications will be loaded here dynamically -->
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <main class="main-content">