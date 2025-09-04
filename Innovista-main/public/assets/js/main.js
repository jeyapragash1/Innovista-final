document.addEventListener('DOMContentLoaded', () => {

    // --- FAQ Accordion ---
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const item = question.parentElement;
            const answer = question.nextElementSibling;

            // Toggle active class on the question button for the icon rotation
            question.classList.toggle('active');

            if (answer.style.maxHeight) {
                // If it's open, close it
                answer.style.maxHeight = null;
            } else {
                // If it's closed, open it
                answer.style.maxHeight = answer.scrollHeight + "px";
            } 
        });
    });

    // --- Add any other homepage-specific scripts here ---
    // For example, the counter animation for the achievements section

});