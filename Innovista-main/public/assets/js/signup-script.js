// document.addEventListener('DOMContentLoaded', function() {
//     // --- User Type Toggle ---
//     const userTypeButtons = document.querySelectorAll('.user-type-btn');
//     const userTypeInput = document.getElementById('userType');
//     const customerFields = document.getElementById('customerFields');
//     const providerFields = document.getElementById('providerFields');

//     userTypeButtons.forEach(button => {
//         button.addEventListener('click', () => {
//             userTypeButtons.forEach(btn => btn.classList.remove('active'));
//             button.classList.add('active');
            
//             const type = button.dataset.type;
//             userTypeInput.value = type;

//             if (type === 'customer') {
//                 customerFields.classList.add('active');
//                 providerFields.classList.remove('active');
//             } else {
//                 providerFields.classList.add('active');
//                 customerFields.classList.remove('active');
//             }
//         });
//     });

//     // --- Form Validation ---
//     const signupForm = document.getElementById('signupForm');
//     signupForm.addEventListener('submit', function(e) {
//         e.preventDefault();
//         if (validateForm()) {
//             alert('Sign up successful! (Form submission logic goes here)');
//             signupForm.reset();
//         }
//     });

//     function validateForm() {
//         let isValid = true;
//         const userType = userTypeInput.value;
        
//         // Clear all previous errors
//         document.querySelectorAll('.error').forEach(el => el.textContent = '');

//         // Validate common password field
//         const password = document.getElementById('password');
//         const passwordError = document.getElementById('passwordError');
//         if (password.value.length < 8) {
//             passwordError.textContent = 'Password must be at least 8 characters.';
//             isValid = false;
//         }

//         if (userType === 'customer') {
//             // Validate customer fields
//             const fullname = document.getElementById('fullname');
//             const email = document.getElementById('email');
//             const phone = document.getElementById('phone');
//             const address = document.getElementById('address');
            
//             if (!fullname.value.trim()) {
//                 document.getElementById('fullnameError').textContent = 'Full name is required.';
//                 isValid = false;
//             }
//             if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
//                 document.getElementById('emailError').textContent = 'A valid email is required.';
//                 isValid = false;
//             }
//             if (!phone.value.trim() || !/^\d{10}$/.test(phone.value)) {
//                 document.getElementById('phoneError').textContent = 'A valid 10-digit phone number is required.';
//                 isValid = false;
//             }
//             if (!address.value.trim()) {
//                 document.getElementById('addressError').textContent = 'Address is required.';
//                 isValid = false;
//             }
//         } else {
//             // Validate provider fields
//             const providerFullname = document.getElementById('providerFullname');
//             const providerEmail = document.getElementById('providerEmail');
//             const providerPhone = document.getElementById('providerPhone');

//             if (!providerFullname.value.trim()) {
//                 document.getElementById('providerFullnameError').textContent = 'Name is required.';
//                 isValid = false;
//             }
//             if (!providerEmail.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(providerEmail.value)) {
//                 document.getElementById('providerEmailError').textContent = 'A valid email is required.';
//                 isValid = false;
//             }
//             if (!providerPhone.value.trim() || !/^\d{10}$/.test(providerPhone.value)) {
//                 document.getElementById('providerPhoneError').textContent = 'A valid 10-digit phone number is required.';
//                 isValid = false;
//             }
//         }

//         return isValid;
//     }
// });

document.addEventListener('DOMContentLoaded', function() {
    const userTypeButtons = document.querySelectorAll('.user-type-btn');
    const userTypeInput = document.getElementById('userType');
    const customerFields = document.getElementById('customerFields');
    const providerFields = document.getElementById('providerFields');

    userTypeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            userTypeButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to the clicked button
            this.classList.add('active');

            const type = this.getAttribute('data-type');
            userTypeInput.value = type;

            if (type === 'customer') {
                customerFields.classList.add('active');
                providerFields.classList.remove('active');
            } else {
                providerFields.classList.add('active');
                customerFields.classList.remove('active');
            }
        });
    });
});

// Add a simple CSS rule in your signup.css to handle the show/hide
/*
    In assets/css/signup.css, add:
    .form-fields {
        display: none;
    }
    .form-fields.active {
        display: block;
    }
*/