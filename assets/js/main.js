document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.togglePassword);
            if (!input) return;
            const visible = input.type === 'password';
            input.type = visible ? 'text' : 'password';
            button.textContent = visible ? 'Hide' : 'Show';
            button.setAttribute('aria-pressed', String(visible));
        });
    });
    const currentYear = document.querySelector('[data-current-year]');
    if (currentYear) {
        currentYear.textContent = new Date().getFullYear();
    }
});
