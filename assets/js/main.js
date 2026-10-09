document.addEventListener('DOMContentLoaded', function () {
    const currentYear = document.querySelector('[data-current-year]');
    if (currentYear) {
        currentYear.textContent = new Date().getFullYear();
    }
});
