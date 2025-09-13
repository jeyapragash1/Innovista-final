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

    <script>
    // Force back button to loop to the provider dashboard
    (function(){
        var dashboardUrl = 'provider_dashboard.php';
        if (window.history && history.pushState) {
            history.replaceState(null, document.title, location.href);
            history.pushState(null, document.title, location.href);
            window.addEventListener('popstate', function () {
                location.replace(dashboardUrl);
            });
        }
        window.addEventListener('pageshow', function(e){
            if (e.persisted) { location.replace(dashboardUrl); }
        });
    })();
    </script>

    <style>
    /* Fallback flash message style for guaranteed visibility */
    .flash-message-container {
        position: fixed;
        top: 32px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        width: 100%;
        max-width: 420px;
        text-align: center;
        pointer-events: none;
    }
    .flash-message {
        padding: 1rem 1.5rem 1rem 1.5rem;
        border-radius: 10px;
        font-size: 1.13rem;
        font-weight: 600;
        margin: 0 auto 0.7rem auto;
        display: inline-block;
        box-shadow: 0 4px 24px rgba(0,0,0,0.13);
        letter-spacing: 0.5px;
        animation: fadeInScale 0.7s cubic-bezier(.4,2,.6,1) both;
        position: relative;
        pointer-events: auto;
        transition: opacity 0.5s;
        background: #e6f9ed;
        color: #1a7f4f;
        border: 1.5px solid #1a7f4f;
    }
    .flash-message.error {
        background: #ffeaea;
        color: #c0392b;
        border: 1.5px solid #c0392b;
    }
    .flash-message .flash-close-btn {
        position: absolute;
        top: 8px;
        right: 12px;
        background: none;
        border: none;
        color: #888;
        font-size: 1.2rem;
        cursor: pointer;
        font-weight: bold;
        transition: color 0.2s;
        z-index: 2;
        padding: 0;
    }
    .flash-message .flash-close-btn:hover {
        color: #c0392b;
    }
    .flash-message.hide {
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.5s;
    }
    @keyframes fadeInScale {
        0% { opacity: 0; transform: scale(0.95) translateY(-10px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    </style>

</head>
<body class="dashboard-body"> <!-- Add class to body -->
    <!-- Flash message for profile update (guaranteed visibility) -->
    <?php if (function_exists('display_flash_message')): ?>
    <div class="flash-message-container" id="flashMessageContainer">
        <?php ob_start(); display_flash_message(); $msg = ob_get_clean();
        if (trim($msg)) {
            // Add close button if message exists
            $msg = preg_replace('/(<div class=\'flash-message [^\']+\'>)/', '$1<button class=\"flash-close-btn\" onclick=\"closeFlashMessage(event)\">&times;</button>', $msg);
            echo $msg;
        }
        ?>
    </div>
    <script>
    function closeFlashMessage(e) {
        var msg = e.target.closest('.flash-message');
        if(msg) { msg.classList.add('hide'); setTimeout(function(){ msg.remove(); }, 500); }
    }
    window.addEventListener('DOMContentLoaded', function() {
        var msg = document.querySelector('.flash-message');
        if(msg) {
            setTimeout(function(){ msg.classList.add('hide'); setTimeout(function(){ msg.remove(); }, 500); }, 3500);
        }
    });
    </script>
    <?php endif; ?>
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
            <header class="main-header-bar" style="display: flex; justify-content: space-between; align-items: center;">
                <div class="header-left" style="display: flex; align-items: center; gap: 1.5rem;">
                    <button class="menu-toggle" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="user-welcome">
                        <span>Welcome, <?php echo $_SESSION['user_name'] ?? 'Provider'; ?></span>
                        <i class="fas fa-user-circle"></i>
                    </div>
                </div>
                <div class="header-right" style="display: flex; align-items: center; gap: 1.5rem;">
                    <!-- Notification Bell -->
                    <div class="notification-bell-wrapper" style="position: relative;">
                        <button id="notificationBell" style="background: none; border: none; cursor: pointer; position: relative;">
                            <i class="fas fa-bell" style="font-size: 1.7rem; color: #0d9488;"></i>
                            <span id="notificationCount" style="display:none; position: absolute; top: -6px; right: -6px; background: #e11d48; color: #fff; border-radius: 50%; font-size: 0.8rem; padding: 2px 6px; font-weight: 700;">0</span>
                        </button>
                        <div id="notificationDropdown" style="display:none; position: absolute; right: 0; top: 2.2rem; background: #fff; box-shadow: 0 4px 16px rgba(0,0,0,0.13); border-radius: 10px; min-width: 320px; max-width: 400px; z-index: 1001; overflow: hidden;">
                            <div style="padding: 1rem; border-bottom: 1px solid #eee; font-weight: 600; color: #222;">Notifications</div>
                            <div id="notificationList" style="max-height: 320px; overflow-y: auto;">
                                <div style="padding: 1rem; color: #888; text-align: center;">No notifications yet.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <script>
            // Notification bell toggle
            document.addEventListener('DOMContentLoaded', function() {
                var bell = document.getElementById('notificationBell');
                var dropdown = document.getElementById('notificationDropdown');
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });
                document.addEventListener('click', function(e) {
                    if (dropdown.style.display === 'block') dropdown.style.display = 'none';
                });
            });
            </script>
            <main class="main-content">