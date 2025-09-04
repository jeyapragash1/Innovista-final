document.addEventListener('DOMContentLoaded', () => {

    // --- Services Page Tab Functionality ---
    const serviceTabs = document.querySelectorAll('.service-tab');
    const serviceCards = document.querySelectorAll('.service-card');

    if (serviceTabs.length > 0 && serviceCards.length > 0) {
        serviceTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetService = tab.getAttribute('data-service');

                // Update tabs
                serviceTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Update content cards
                serviceCards.forEach(card => {
                    if (card.getAttribute('data-service') === targetService) {
                        card.classList.add('active');
                    } else {
                        card.classList.remove('active');
                    }
                });
            });
        });
    }

    // --- Services Hero Slider ---
    const heroSlides = document.querySelectorAll('.services-hero .hero-slide');
    let currentSlide = 0;

    function showNextSlide() {
        if (heroSlides.length === 0) return;
        heroSlides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % heroSlides.length;
        heroSlides[currentSlide].classList.add('active');
    }

    if (heroSlides.length > 1) {
        setInterval(showNextSlide, 5000); // Change image every 5 seconds
    }
});