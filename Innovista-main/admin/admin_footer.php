            </main> <!-- end .main-content -->
        </div> <!-- end .content-wrapper -->
    </div> <!-- end .admin-container -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const adminContainer = document.querySelector('.admin-container');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            adminContainer.classList.toggle('sidebar-active');
        });
    }
});
</script>
</body>
</html>