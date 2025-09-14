<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\includes\user_dashboard_header.php

// This file is included AFTER session.php and protectPage() has been called by the dashboard page itself.
// So, $_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_role'] are guaranteed to be set
// and the user is confirmed to be a customer or admin.
// $pageTitle is expected to be set by the calling page (e.g., customer_dashboard.php).

// Ensure session and auth functions are available
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!function_exists('isUserLoggedIn')) {
    require_once '../public/session.php'; // Defines helper functions
}
if (!function_exists('protectPage')) { // Define protectPage if not already defined (e.g., in session.php)
    function protectPage(string $requiredRole): void {
        if (!isUserLoggedIn()) {
            header("Location: ../public/login.php");
            exit();
        }
        if (getUserRole() !== $requiredRole && getUserRole() !== 'admin') { 
            set_flash_message('error', 'Access denied. You do not have permission to view this page.');
            header("Location: ../public/index.php");
            exit();
        }
    }
}
// This header is designed for customer-specific pages, so we protect it here.
// The individual dashboard pages will call protectPage('customer') before this include.

$loggedInUserRole = getUserRole(); // Get the actual role from session
$loggedInUserName = htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); // Fallback name for display
$currentPage = basename($_SERVER['SCRIPT_NAME']); // e.g., 'customer_dashboard.php'

// Get user's profile image path for the header (assuming getImageSrc is in session.php)
$profile_image_path = getImageSrc($_SESSION['profile_image_path'] ?? 'assets/images/default-avatar.jpg');
// You need to ensure $_SESSION['profile_image_path'] is set during login for this.
// If not, fetch it here:
if (!isset($_SESSION['profile_image_path'])) {
    require_once '../config/Database.php';
    $db_conn = (new Database())->getConnection();
    $stmt_profile = $db_conn->prepare("SELECT profile_image_path FROM users WHERE id = :id");
    
    // FIX: Assign getUserId() to a variable first
    $current_user_id = getUserId(); 
    $stmt_profile->bindParam(':id', $current_user_id, PDO::PARAM_INT); // Use the variable
    
    $stmt_profile->execute();
    $profile_img_row = $stmt_profile->fetch(PDO::FETCH_ASSOC);
    if ($profile_img_row && $profile_img_row['profile_image_path']) {
        $_SESSION['profile_image_path'] = $profile_img_row['profile_image_path'];
        $profile_image_path = getImageSrc($profile_img_row['profile_image_path']);
    }
}


// --- Customer-specific navigation links ---
$navLinks = [
    ['href' => 'customer_dashboard.php', 'icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard'],
    ['href' => 'request_quotation.php', 'icon' => 'fas fa-file-signature', 'text' => 'New Quote'],
    ['href' => 'my_projects.php', 'icon' => 'fas fa-tasks', 'text' => 'My Projects'],
    ['href' => 'payment_history.php', 'icon' => 'fas fa-receipt', 'text' => 'Payments'],
    ['href' => 'my_profile.php', 'icon' => 'fas fa-user-edit', 'text' => 'My Profile'],
    // Add more customer-specific links here
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Customer Dashboard'); ?> - Innovista</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Dashboard Specific Stylesheets -->
    <link rel="stylesheet" href="../public/assets/css/main.css">
    <link rel="stylesheet" href="../public/assets/css/dashboard.css"> <!-- General dashboard layout, stats, tables -->
    <link rel="stylesheet" href="../public/assets/css/customer-dashboard.css"> <!-- Customer-specific overrides/colors -->
    <link rel="stylesheet" href="../public/assets/css/notifications.css">
    
    <!-- Add this before closing head tag -->
    <script defer src="../public/assets/js/notifications.js"></script>

</head>
<body>
    <div class="customer-dashboard-container">
        <aside class="dashboard-sidebar" id="dashboard-sidebar">
            <div class="sidebar-header">
                <!-- Path from includes/ to public/index.php -->
                <a href="../public/index.php" class="sidebar-logo">
                    <img src="../public/assets/images/logo1.png" alt="Innovista Logo">
                    <span>Innovista</span>
                </a>
            </div>
            <nav class="sidebar-nav">
                <?php foreach ($navLinks as $link): ?>
                    <?php
                        $linkFileName = basename($link['href']);
                        $isActive = ($currentPage === $linkFileName);
                    ?>
                    <a href="<?php echo htmlspecialchars($link['href']); ?>" class="nav-link <?php echo $isActive ? 'active' : ''; ?>">
                        <i class="<?php echo htmlspecialchars($link['icon']); ?>"></i><span><?php echo htmlspecialchars($link['text']); ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="sidebar-footer">
                <a href="../public/logout.php" class="nav-link logout">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </a>
            </div>
        </aside>
        
        <div class="dashboard-content-wrapper">
            <header class="dashboard-main-header">
                <div class="header-left">
                    <button class="dashboard-menu-toggle" id="dashboard-menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="user-welcome">
                        <span>Welcome, <?php echo $loggedInUserName; ?></span>
                        <img src="<?php echo htmlspecialchars($profile_image_path); ?>" alt="User Avatar" class="dashboard-avatar-sm">
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
                <script>
                // Notification bell toggle (same as provider)
                document.addEventListener('DOMContentLoaded', function() {
                    var bell = document.getElementById('notificationBell');
                    var dropdown = document.getElementById('notificationDropdown');
                    var notificationCount = document.getElementById('notificationCount'); // Added

                    // Fetch initial notification count (AJAX call to a backend script)
                    function fetchNotificationCount() {
                        // Ensure getUserId() is available (from session.php)
                        const userId = <?php echo json_encode(getUserId()); ?>;
                        if (!userId) return; // Don't fetch if user ID is not set

                        fetch('../handlers/fetch_notifications.php?action=count_unread&user_id=' + userId)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.count > 0) {
                                    notificationCount.textContent = data.count;
                                    notificationCount.style.display = 'block';
                                } else {
                                    notificationCount.style.display = 'none';
                                }
                            })
                            .catch(error => console.error('Error fetching notification count:', error));
                    }

                    // Fetch and display full notifications
                    function fetchNotifications() {
                        const userId = <?php echo json_encode(getUserId()); ?>;
                        if (!userId) return;

                        fetch('../handlers/fetch_notifications.php?action=get_all&user_id=' + userId)
                            .then(response => response.json())
                            .then(data => {
                                const notificationList = document.getElementById('notificationList');
                                notificationList.innerHTML = ''; // Clear previous

                                if (data.success && data.notifications.length > 0) {
                                    data.notifications.forEach(notif => {
                                        const notifItem = document.createElement('div');
                                        notifItem.className = `notification-item ${notif.is_read == 0 ? 'unread' : 'read'}`;
                                        notifItem.innerHTML = `
                                            <div class="notification-icon"><i class="${notif.icon_class}"></i></div>
                                            <div class="notification-content">
                                                <p class="notification-text">${notif.message}</p>
                                                <span class="notification-time">${notif.time_ago} ago</span>
                                            </div>
                                        `;
                                        notifItem.addEventListener('click', function() {
                                            // Mark as read and redirect
                                            fetch(`../handlers/fetch_notifications.php?action=mark_read&id=${notif.id}`, { method: 'POST' })
                                                .then(res => res.json())
                                                .then(resData => {
                                                    if (resData.success) {
                                                        fetchNotificationCount(); // Refresh count after marking read
                                                        if (notif.link) {
                                                            window.location.href = notif.link;
                                                        } else {
                                                            location.reload(); // Just reload if no specific link
                                                        }
                                                    }
                                                });
                                        });
                                        notificationList.appendChild(notifItem);
                                    });
                                } else {
                                    notificationList.innerHTML = '<div style="padding: 1rem; color: var(--text-light); text-align: center;">No notifications yet.</div>';
                                }
                            })
                            .catch(error => console.error('Error fetching notifications:', error));
                    }


                    // Initial fetch
                    fetchNotificationCount();
                    // Optional: auto-refresh notifications every X seconds
                    // setInterval(fetchNotificationCount, 30000); // Every 30 seconds

                    bell.addEventListener('click', function(e) {
                        e.stopPropagation();
                        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                        if (dropdown.style.display === 'block') {
                            fetchNotifications(); // Fetch full list when dropdown is opened
                        }
                    });
                    document.addEventListener('click', function(e) {
                        if (dropdown.style.display === 'block' && !dropdown.contains(e.target) && !bell.contains(e.target)) {
                            dropdown.style.display = 'none';
                        }
                    });
                });
                </script>
            </header>
            <main class="dashboard-main-content">