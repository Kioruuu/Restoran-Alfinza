// Mobile menu toggle
document.querySelector('button').addEventListener('click', () => {
    document.querySelector('.sidebar').classList.toggle('-translate-x-full');
});

// Add smooth scroll behavior
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
}); 