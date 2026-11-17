document.addEventListener('DOMContentLoaded', () => {
    // Highlight selected blood group pill immediately on click in find _donors.php
    const pills = document.querySelectorAll('.pill');
    pills.forEach(pill => {
        pill.addEventListener('click', () => {
            pills.forEach(p => p.classList.remove('selected'));
            pill.classList.add('selected');
        });
    });
});
