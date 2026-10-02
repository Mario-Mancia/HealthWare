document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            // Alternar el tipo de input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Alternar el icono de Material Design
            this.textContent = type === 'password' ? 'visibility_off' : 'visibility';

            // Efecto visual al interactuar
            this.style.color = type === 'password' ? 'var(--md-sys-color-outline)' : 'var(--bs-primary)';
        });
    }
});
