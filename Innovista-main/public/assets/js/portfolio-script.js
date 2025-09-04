document.addEventListener('DOMContentLoaded', () => {

    // --- Portfolio Filtering Functionality ---
    const filterButtons = document.querySelectorAll('.filter-btn');
    const projectCards = document.querySelectorAll('.project-card');

    if (filterButtons.length > 0 && projectCards.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Set active state on button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.dataset.filter;

                projectCards.forEach(card => {
                    if (filter === 'all' || card.dataset.category === filter) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // --- Before & After Slider Functionality ---
    const slider = document.querySelector('.ba-slider');
    if (slider) {
        const resize = slider.querySelector('.resize');
        const handle = slider.querySelector('.handle');
        let isDragging = false;

        const startDragging = (e) => {
            isDragging = true;
            // Prevent text selection while dragging
            e.preventDefault();
        };

        const stopDragging = () => {
            isDragging = false;
        };

        const onDrag = (e) => {
            if (!isDragging) return;

            // Use touch or mouse position
            const x = e.clientX || e.touches[0].clientX;
            const sliderRect = slider.getBoundingClientRect();
            let newWidth = ((x - sliderRect.left) / sliderRect.width) * 100;

            // Clamp the value between 0 and 100
            if (newWidth < 0) newWidth = 0;
            if (newWidth > 100) newWidth = 100;

            resize.style.width = newWidth + '%';
            handle.style.left = newWidth + '%';
        };

        // Mouse events
        handle.addEventListener('mousedown', startDragging);
        document.addEventListener('mouseup', stopDragging);
        document.addEventListener('mousemove', onDrag);

        // Touch events
        handle.addEventListener('touchstart', startDragging);
        document.addEventListener('touchend', stopDragging);
        document.addEventListener('touchmove', onDrag);
    }
});