const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('show');
        }
    });
});

document.querySelectorAll('.billing-box, .invoice-box, .card, .dashboard-card')
    .forEach(el => observer.observe(el));