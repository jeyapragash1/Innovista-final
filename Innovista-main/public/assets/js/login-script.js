document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const otpSection = document.getElementById('otpSection');
    const successSection = document.getElementById('successSection');
    const allSteps = document.querySelectorAll('.login-step');

    const otpInputsDiv = document.getElementById('otpInputs');
    const otpVerifyBtn = document.getElementById('otpVerifyBtn');
    const otpBackBtn = document.getElementById('otpBackBtn');

    function showStep(stepToShow) {
        allSteps.forEach(step => step.classList.remove('active'));
        stepToShow.classList.add('active');
    }

    // Login form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        let isValid = true;
        
        // Simple validation
        if (!email) {
            document.getElementById('emailError').textContent = 'Email is required';
            isValid = false;
        } else {
            document.getElementById('emailError').textContent = '';
        }
        
        if (!password) {
            document.getElementById('passwordError').textContent = 'Password is required';
            isValid = false;
        } else {
            document.getElementById('passwordError').textContent = '';
        }

        if (isValid) {
            console.log('Form valid, proceeding to OTP');
            setupOtpInputs();
            showStep(otpSection);
        }
    });

    // Setup OTP input fields
    function setupOtpInputs() {
        otpInputsDiv.innerHTML = ''; // Clear previous inputs
        for (let i = 0; i < 6; i++) {
            const input = document.createElement('input');
            input.type = 'text';
            input.maxLength = 1;
            input.addEventListener('input', () => {
                if (input.value && i < 5) {
                    otpInputsDiv.children[i + 1].focus();
                }
                checkOtpFilled();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && i > 0) {
                    otpInputsDiv.children[i - 1].focus();
                }
            });
            otpInputsDiv.appendChild(input);
        }
        otpInputsDiv.children[0].focus();
    }
    
    // Check if all OTP fields are filled
    function checkOtpFilled() {
        const otp = Array.from(otpInputsDiv.children).map(inp => inp.value).join('');
        otpVerifyBtn.style.display = otp.length === 6 ? 'block' : 'none';
    }
    
    // OTP section buttons
    otpBackBtn.addEventListener('click', () => showStep(loginForm));

    otpVerifyBtn.addEventListener('click', function() {
        console.log('Verifying OTP...');
        showStep(successSection);
        setTimeout(() => {
            window.location.href = 'customer-dashboard.html';
        }, 2000);
    });
});