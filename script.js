const authModal = document.querySelector('.auth-modal');
const loginLink = document.querySelector('.login-link');
const registerLink = document.querySelector('.register-link');
const loginBtnModal = document.querySelectorAll('.login-btn-modal'); // note: querySelectorAll for multiple buttons
const closeBtnModal = document.querySelector('.close-btn-modal');
const profileBox = document.querySelector('.profile-box');
const avatarCircle = document.querySelector('.avatar-circle');
const alertBox = document.querySelector('.alert-box');
const loginForm = document.getElementById('login-form');
const redirectInput = document.getElementById('redirect_after_login');

// Switch between login/register forms
registerLink.addEventListener('click', () => authModal.classList.add('slide'));
loginLink.addEventListener('click', () => authModal.classList.remove('slide'));

// Open login modal and set redirect dynamically
loginBtnModal.forEach(btn => {
    btn.addEventListener('click', () => {
        authModal.classList.add('show');

        // Set redirect target from data-redirect attribute
        const redirectPage = btn.getAttribute('data-redirect') || 'index.php';
        if (redirectInput) redirectInput.value = redirectPage;
    });
});

// Close modal
closeBtnModal.addEventListener('click', () => authModal.classList.remove('show', 'slide'));

// Toggle profile dropdown
if (avatarCircle) avatarCircle.addEventListener('click', () => profileBox.classList.toggle('show'));

// Alerts fade in/out
if (alertBox) {
    setTimeout(() => alertBox.classList.add('show'), 50);

    setTimeout(() => {
        alertBox.classList.remove('show');
        setTimeout(() => alertBox.remove(), 1000);
    }, 6000);
}

// HAMBURGER MENU FUNCTIONALITY
const hamburger = document.querySelector('.hamburger');
const nav = document.querySelector('.header-nav');

if (hamburger && nav) {
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent click from bubbling to document
        nav.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
            nav.classList.remove('active');
        }
    });
}