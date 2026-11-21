document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contact-form');
    if (!form) return;
    document.getElementById('contact-preview-button').disabled = false;
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (!form.reportValidity()) return;
        const values = new FormData(form);
        const company = String(values.get('company') || '').trim();
        document.getElementById('preview-from').textContent = String(values.get('name')).trim()
            + (company ? ' · ' + company : '') + ' (' + String(values.get('email')).trim() + ')';
        document.getElementById('preview-topic').textContent = values.get('topic');
        document.getElementById('preview-message').textContent = String(values.get('message')).trim();
        const preview = document.getElementById('contact-preview');
        preview.hidden = false;
        preview.focus();
    });
});
