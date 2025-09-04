document.addEventListener('DOMContentLoaded', function() {
    const bookingModal = document.getElementById('bookingModal');
    const closeModalBtn = document.querySelector('.close-modal-btn');
    const bookBtns = document.querySelectorAll('.btn-book-consultation');
    const calendarStep = document.getElementById('calendarStep');
    const paymentStep = document.getElementById('paymentStep');
    const calendarDates = document.querySelectorAll('.calendar-date:not(.other-month)');
    const backToCalendarBtn = document.getElementById('backToCalendar');

    // Function to open the modal
    const openModal = () => {
        if (bookingModal) {
            bookingModal.classList.add('active');
            // Reset to calendar view
            calendarStep.style.display = 'block';
            paymentStep.style.display = 'none';
        }
    };

    // Function to close the modal
    const closeModal = () => {
        if (bookingModal) {
            bookingModal.classList.remove('active');
        }
    };

    // Attach event listeners
    bookBtns.forEach(btn => btn.addEventListener('click', openModal));
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeModal);
    if (backToCalendarBtn) backToCalendarBtn.addEventListener('click', () => {
        calendarStep.style.display = 'block';
        paymentStep.style.display = 'none';
    });

    // Handle clicking outside the modal
    window.addEventListener('click', (event) => {
        if (event.target === bookingModal) {
            closeModal();
        }
    });

    // Handle calendar date selection
    calendarDates.forEach(date => {
        date.addEventListener('click', () => {
            // Remove selected from any other date
            calendarDates.forEach(d => d.classList.remove('selected'));
            // Add selected to the clicked date
            date.classList.add('selected');

            // Switch to payment view after a short delay
            setTimeout(() => {
                calendarStep.style.display = 'none';
                paymentStep.style.display = 'block';
            }, 300);
        });
    });
});