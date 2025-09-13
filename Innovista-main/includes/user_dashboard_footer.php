<?php
// C:\xampp1\htdocs\Innovista-final\Innovista-main\includes\user_dashboard_footer.php
?>
            </main> <!-- end .dashboard-main-content -->
        </div> <!-- end .dashboard-content-wrapper -->
    </div> <!-- end .customer-dashboard-container / user-dashboard-container -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('dashboard-menu-toggle'); // Correct ID
    const sidebar = document.getElementById('dashboard-sidebar'); // Correct ID
    const container = document.querySelector('.customer-dashboard-container, .user-dashboard-container'); // Selector for main container
    const contentWrapper = document.querySelector('.dashboard-content-wrapper');

    if (menuToggle && sidebar && container && contentWrapper) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            container.classList.toggle('sidebar-active'); // Toggle class on main container
        });
    }

    // Close sidebar when clicking on overlay in mobile view
    if (contentWrapper && sidebar && container) {
        contentWrapper.addEventListener('click', function(event) {
            // Check if sidebar is active and click is outside the sidebar content
            // The overlay is created by .user-dashboard-container.sidebar-active::before
            // If the click target is the overlay (which is part of contentWrapper's background), close it.
            if (sidebar.classList.contains('active') && !sidebar.contains(event.target)) {
                // Check if the click was on the overlay itself, not a child element within content
                if (event.target === contentWrapper || event.target.closest('main')) { // Clicks on main content or wrapper
                     sidebar.classList.remove('active');
                     container.classList.remove('sidebar-active');
                }
            }
        });
    }

    // Notification bell toggle (moved from user_dashboard_header for better JS separation)
    // This script should be in dashboard_footer if you want to avoid code duplication across user_dashboard_header.php
    // Make sure it's run AFTER the HTML for the bell and dropdown is loaded.
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    const notificationCount = document.getElementById('notificationCount');

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


    if (bell && dropdown) {
        fetchNotificationCount(); // Initial fetch
        bell.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent document click from closing immediately
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            if (dropdown.style.display === 'block') {
                fetchNotifications(); // Fetch full list when dropdown is opened
            }
        });
        document.addEventListener('click', function(e) {
            // Close dropdown if click is outside bell and dropdown itself
            if (dropdown.style.display === 'block' && !bell.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

});
</script>
</body>
</html>