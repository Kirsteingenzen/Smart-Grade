window.addEventListener('DOMContentLoaded', event => {

    // Navbar shrink function
    var navbarShrink = function () {
        const navbarCollapsible = document.body.querySelector('#mainNav');
        if (!navbarCollapsible) return;
        if (window.scrollY === 0) {
            navbarCollapsible.classList.remove('navbar-shrink');
        } else {
            navbarCollapsible.classList.add('navbar-shrink');
        }
    };

    // Shrink the navbar 
    navbarShrink();
    document.addEventListener('scroll', navbarShrink);

    // Bootstrap scrollspy
    const mainNav = document.body.querySelector('#mainNav');
    if (mainNav) {
        new bootstrap.ScrollSpy(document.body, {
            target: '#mainNav',
            rootMargin: '0px 0px -40%',
        });
    }

    // Collapse responsive navbar
    const navbarToggler = document.body.querySelector('.navbar-toggler');
    const responsiveNavItems = [].slice.call(document.querySelectorAll('#navbarResponsive .nav-link'));
    responsiveNavItems.map(item => {
        item.addEventListener('click', () => {
            if (window.getComputedStyle(navbarToggler).display !== 'none') {
                navbarToggler.click();
            }
        });
    });

    // SimpleLightbox
    new SimpleLightbox({
        elements: '#portfolio a.portfolio-box'
    });

    // ===============================
    // Custom: Show Login Popup Modal
    // ===============================
    const loginBtn = document.querySelector('a[href="#login"]');
    const loginPopup = document.getElementById('loginPopup');
    const closeBtn = document.getElementById('closePopup');
    const teacherLoginBtn = document.getElementById('teacherLoginBtn');
    const studentLoginBtn = document.getElementById('studentLoginBtn');
    const teacherLoginForm = document.getElementById('teacherLoginForm');
    const studentLoginForm = document.getElementById('studentLoginForm');
    const roleSelection = document.getElementById('roleSelection');

    if (loginBtn && loginPopup) {
        loginBtn.addEventListener('click', e => {
            e.preventDefault();
            loginPopup.classList.remove('d-none');
            teacherLoginForm.classList.add('d-none');
            studentLoginForm.classList.add('d-none');
            roleSelection.classList.remove('d-none');
        });
    }

    if (closeBtn && loginPopup) {
        closeBtn.addEventListener('click', () => {
            loginPopup.classList.add('d-none');
            teacherLoginForm.classList.add('d-none');
            studentLoginForm.classList.add('d-none');
            roleSelection.classList.remove('d-none');
        });
    }

    if (teacherLoginBtn && teacherLoginForm && roleSelection) {
        teacherLoginBtn.addEventListener('click', e => {
            e.preventDefault();
            roleSelection.classList.add('d-none');
            teacherLoginForm.classList.remove('d-none');
        });
    }

    if (studentLoginBtn && studentLoginForm && roleSelection) {
        studentLoginBtn.addEventListener('click', e => {
            e.preventDefault();
            roleSelection.classList.add('d-none');
            studentLoginForm.classList.remove('d-none');
        });
    }

    // ===============================
    // Teacher Login Validation + Redirect
    // ===============================
    const loginForm = document.getElementById('teacherLoginFormElement');
    const emailInput = document.getElementById('teacherEmail');
    const passwordInput = document.getElementById('teacherPassword');
    const passwordFeedback = document.getElementById('passwordFeedback');

    if (loginForm && emailInput && passwordInput) {
        loginForm.addEventListener('submit', e => {
            e.preventDefault(); // prevent form from refreshing page
            let valid = true;

            // Email validation - **SKIP** this validation for a plain username
            // const emailOK = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            // If you are using "teacher" as the username, we will skip email format validation.
            const username = emailInput.value.trim();
            if (username === '') {
                valid = false;
                emailInput.classList.add('is-invalid');
            } else {
                emailInput.classList.remove('is-invalid');
            }

            // Password validation - allow simple password like "123"
            const password = passwordInput.value.trim();
            if (password === '') {
                valid = false;
                passwordInput.classList.add('is-invalid');
                passwordFeedback.style.display = 'block';
            } else {
                passwordInput.classList.remove('is-invalid');
                passwordFeedback.style.display = 'none';
            }

            // If valid, submit the form (this is where you handle the login via PHP backend)
            if (valid) {
                loginForm.submit();
            }
        });
    }

    // ===============================
    // Student Login Validation + Redirect
    // ===============================
    const studentForm = document.getElementById('studentLoginFormElement');
    const studentIDInput = document.getElementById('studentID');
    const studentPasswordInput = document.getElementById('studentPassword');
    const studentPasswordFeedback = document.getElementById('studentPasswordFeedback');

    if (studentForm && studentIDInput && studentPasswordInput) {
        studentForm.addEventListener('submit', e => {
            e.preventDefault(); // Prevent form from submitting normally
            let valid = true;

            // Validate ID
            if (studentIDInput.value.trim() === '') {
                valid = false;
                studentIDInput.classList.add('is-invalid');
            } else {
                studentIDInput.classList.remove('is-invalid');
            }

            // Validate Password
            const pwd = studentPasswordInput.value;
            const pwdOK = /^(?=.*\d).{8,}$/.test(pwd);
            if (!pwdOK) {
                valid = false;
                studentPasswordInput.classList.add('is-invalid');
                studentPasswordFeedback.style.display = 'block';
            } else {
                studentPasswordInput.classList.remove('is-invalid');
                studentPasswordFeedback.style.display = 'none';
            }

            // ✅ If valid, redirect to student dashboard
            if (valid) {
                studentForm.submit();
            }
        });
    }

});
