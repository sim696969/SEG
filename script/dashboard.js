document.addEventListener('DOMContentLoaded', () => {
    // --- Feedback Modal Logic ---
    const feedbackBtn = document.getElementById('feedbackBtn');
    const modal = document.getElementById('feedbackModal');
    const closeBtn = document.querySelector('.close-btn');

    const openModal = () => { if (modal) modal.style.display = 'flex'; };
    const closeModal = () => { if (modal) modal.style.display = 'none'; };

    if (feedbackBtn) {
        feedbackBtn.addEventListener('click', openModal);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }
    if (modal) {
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // --- Star Rating Logic ---
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('ratingInput');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const ratingValue = star.getAttribute('data-value');
            if (ratingInput) ratingInput.value = ratingValue;
            updateStarsUI(ratingValue);
        });
    });

    function updateStarsUI(rating) {
        stars.forEach(star => {
            star.classList.toggle('filled', star.getAttribute('data-value') <= rating);
        });
    }
});
