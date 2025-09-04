document.addEventListener('DOMContentLoaded', () => {

    // --- Mobile Menu Toggle ---
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');

    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', () => {
            // Toggle the 'active' class on both the hamburger button and the menu
            navbarToggle.classList.toggle('active');
            navbarMenu.classList.toggle('active');

            // Bonus: Prevent body from scrolling when menu is open
            if (navbarMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
    }

    // --- Header Scrolled Effect ---
    const header = document.querySelector('.main-header');
    
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) { // Add 'scrolled' class after 50px of scrolling
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

});