   </main> <!-- end .main-content -->
        </div> <!-- end .content-wrapper -->
    </div> <!-- end .admin-container -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const adminContainer = document.querySelector('.admin-container');
    const contentWrapper = document.querySelector('.content-wrapper'); // Added for overlay click

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            adminContainer.classList.toggle('sidebar-active');
        });
    }

    // Close sidebar when clicking on overlay in mobile view
    if (contentWrapper) {
        contentWrapper.addEventListener('click', function(event) {
            // Only close if sidebar is active and click is outside actual sidebar content
            if (sidebar.classList.contains('active') && !sidebar.contains(event.target) && event.target === contentWrapper.children[0]) {
                 sidebar.classList.remove('active');
                 adminContainer.classList.remove('sidebar-active');
            }
        });
    }
});
</script>
</body>
</html>