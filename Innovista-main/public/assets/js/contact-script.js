document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateContactForm()) {
                // Here you would typically send the form data to your backend
                alert('Thank you for your message! We will get back to you shortly.');
                contactForm.reset();
            }
        });
    }

    function validateContactForm() {
        let isValid = true;
        
        // Clear previous errors
        document.querySelectorAll('.error').forEach(el => el.textContent = '');

        // Validate Name
        const name = document.getElementById('name');
        if (!name.value.trim()) {
            document.getElementById('nameError').textContent = 'Name is required.';
            isValid = false;
        }

        // Validate Email
        const email = document.getElementById('email');
        if (!email.value.trim()) {
            document.getElementById('emailError').textContent = 'Email is required.';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
            document.getElementById('emailError').textContent = 'Please enter a valid email address.';
            isValid = false;
        }

        // Validate Subject
        const subject = document.getElementById('subject');
        if (!subject.value.trim()) {
            document.getElementById('subjectError').textContent = 'Subject is required.';
            isValid = false;
        }

        // Validate Message
        const message = document.getElementById('message');
        if (!message.value.trim()) {
            document.getElementById('messageError').textContent = 'Message cannot be empty.';
            isValid = false;
        }

        return isValid;
    }
});