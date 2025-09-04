document.addEventListener('DOMContentLoaded', function() {
    var bookingModal = document.getElementById('bookingModal');
    var closeBookingModalBtn = bookingModal ? bookingModal.querySelector('.close-modal-btn') : null;
    var confirmBookingBtn = document.getElementById('btnConfirmBooking');
    var bookingForm = document.getElementById('bookingForm');
    var otpSection = document.getElementById('otpSection');
    var otpInput = document.getElementById('otpInput');
    var submitOtpBtn = document.getElementById('submitOtpBtn');
    var bookingConfirmedMsg = document.getElementById('bookingConfirmedMsg');
    var generatedOtp = '';

    if (confirmBookingBtn && bookingModal) {
        confirmBookingBtn.addEventListener('click', function() {
            bookingModal.classList.add('active');
        });
    }
    if (closeBookingModalBtn) {
        closeBookingModalBtn.addEventListener('click', function() {
            if (window.parent !== window) {
                window.parent.postMessage('closeBookingModal', '*');
            }
        });
    }
    window.addEventListener('click', function(e) {
        if (bookingModal && e.target === bookingModal) {
            bookingModal.classList.remove('active');
            if (bookingForm) bookingForm.style.display = '';
            if (otpSection) otpSection.style.display = 'none';
            if (bookingConfirmedMsg) bookingConfirmedMsg.style.display = 'none';
        }
    });
    // Handle booking form submit
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Simulate OTP generation and sending
            generatedOtp = String(Math.floor(100000 + Math.random() * 900000));
            // In real app, send OTP to user via SMS/email
            if (otpSection) otpSection.style.display = 'flex';
            bookingForm.style.display = 'none';
            if (bookingConfirmedMsg) bookingConfirmedMsg.style.display = 'none';
            alert('Your OTP is: ' + generatedOtp + ' (for demo purposes)');
        });
    }
    // Handle OTP submit
    if (submitOtpBtn) {
        submitOtpBtn.addEventListener('click', function() {
            if (otpInput.value === generatedOtp) {
                if (otpSection) otpSection.style.display = 'none';
                if (bookingConfirmedMsg) bookingConfirmedMsg.style.display = 'block';
            } else {
                alert('Invalid OTP. Please try again.');
            }
        });
    }
    // Gender button selection
    var genderMaleBtn = document.getElementById('genderMale');
    var genderFemaleBtn = document.getElementById('genderFemale');
    var genderInput = document.getElementById('gender');
    if (genderMaleBtn && genderFemaleBtn && genderInput) {
        genderMaleBtn.addEventListener('click', function() {
            genderMaleBtn.classList.add('selected');
            genderFemaleBtn.classList.remove('selected');
            genderInput.value = 'Male';
        });
        genderFemaleBtn.addEventListener('click', function() {
            genderFemaleBtn.classList.add('selected');
            genderMaleBtn.classList.remove('selected');
            genderInput.value = 'Female';
        });
    }
});
