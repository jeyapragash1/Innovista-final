document.addEventListener('DOMContentLoaded', function() {
    const notificationsToggle = document.getElementById('notificationsToggle');
    const notificationsDropdown = document.getElementById('notificationsDropdown');
    const notificationsList = document.getElementById('notificationsList');
    const notificationCount = document.getElementById('notificationCount');
    const markAllReadBtn = document.querySelector('.mark-all-read');
    let lastNotificationCount = 0; // Track previous notification count

    // Toggle notifications dropdown
    notificationsToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationsDropdown.classList.toggle('show');
        if (notificationsDropdown.classList.contains('show')) {
            fetchNotifications();
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationsDropdown.contains(e.target) && !notificationsToggle.contains(e.target)) {
            notificationsDropdown.classList.remove('show');
        }
    });

    // Mark all notifications as read
    markAllReadBtn.addEventListener('click', function() {
        fetch('../handlers/handle_notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'mark_all_read'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item.unread')
                    .forEach(item => item.classList.remove('unread'));
                updateNotificationCount(0);
            }
        });
    });

    // Fetch notifications
    function fetchNotifications() {
        fetch('../handlers/handle_notifications.php')
            .then(response => response.json())
            .then(data => {
                updateNotifications(data.notifications);
                updateNotificationCount(data.unreadCount);
            });
    }

    // Update notification count
    function updateNotificationCount(count) {
        notificationCount.textContent = count;
        notificationCount.style.display = count > 0 ? 'block' : 'none';
    }

    // Update notifications list
    function updateNotifications(notifications) {
        if (!notifications.length) {
            notificationsList.innerHTML = `
                <div class="notification-empty">
                    <i class="fas fa-bell-slash"></i>
                    <p>No new notifications</p>
                </div>`;
            return;
        }

        notificationsList.innerHTML = '';
        
        notifications.forEach(notification => {
            const item = document.createElement('div');
            item.className = `notification-item ${notification.read ? '' : 'unread'}`;
            
            // Format the time
            const timeAgo = formatTimeAgo(new Date(notification.created_at));
            
            // Add icon based on notification type
            const icon = getNotificationIcon(notification.type);
            
            item.innerHTML = `
                <div class="notification-title">
                    <i class="${icon}"></i>
                    ${notification.title}
                </div>
                <div class="notification-message">${notification.message}</div>
                <div class="notification-time">${timeAgo}</div>
            `;
            
            // Add animation for new notifications
            if (!notification.read && lastNotificationCount < notifications.filter(n => !n.read).length) {
                item.classList.add('notification-new');
            }
            
            item.addEventListener('click', () => markAsRead(notification.id));
            notificationsList.appendChild(item);
        });
        
        // Update last notification count
        lastNotificationCount = notifications.filter(n => !n.read).length;
    }

    // Mark single notification as read
    function markAsRead(notificationId) {
        fetch('../handlers/handle_notifications.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'mark_read',
                notification_id: notificationId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchNotifications();
            }
        });
    }

    // Initial fetch
    fetchNotifications();

    // Set up polling for new notifications (every 30 seconds)
    setInterval(fetchNotifications, 30000);

    // Helper function to format time ago
    function formatTimeAgo(date) {
        const now = new Date();
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Just now';
        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' minutes ago';
        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hours ago';
        if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' days ago';
        return date.toLocaleDateString();
    }

    // Helper function to get notification icon based on type
    function getNotificationIcon(type) {
        const icons = {
            'quotation': 'fas fa-file-invoice-dollar',
            'project': 'fas fa-tasks',
            'payment': 'fas fa-credit-card',
            'message': 'fas fa-envelope',
            'consultation': 'fas fa-calendar-check',
            'portfolio': 'fas fa-images',
            'dispute': 'fas fa-exclamation-triangle',
            'general': 'fas fa-bell'
        };
        return icons[type] || icons.general;
    }
});
