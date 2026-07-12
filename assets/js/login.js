/**
 * AssetFlow - Login Form Validation
 * Client-side validation using Bootstrap 5 validation patterns.
 */

(function () {
    'use strict';

    const form = document.getElementById('loginForm');

    if (!form) {
        return;
    }

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    /**
     * Validate email format.
     */
    function isValidEmail(value) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailPattern.test(value.trim());
    }

    /**
     * Show or clear validation state on a field.
     */
    function setFieldValidity(input, isValid) {
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
        }
    }

    /**
     * Validate a single field.
     */
    function validateField(input) {
        const value = input.value.trim();

        if (input.id === 'email') {
            return value !== '' && isValidEmail(value);
        }

        if (input.id === 'password') {
            return value.length >= 6;
        }

        return true;
    }

    // Real-time validation on blur
    [emailInput, passwordInput].forEach(function (input) {
        input.addEventListener('blur', function () {
            if (input.value.trim() !== '') {
                setFieldValidity(input, validateField(input));
            }
        });

        // Clear invalid state while typing
        input.addEventListener('input', function () {
            if (input.classList.contains('is-invalid') && validateField(input)) {
                input.classList.remove('is-invalid');
            }
        });
    });

    // Form submission validation
    form.addEventListener('submit', function (event) {
        let isFormValid = true;

        [emailInput, passwordInput].forEach(function (input) {
            const isValid = validateField(input);
            setFieldValidity(input, isValid);

            if (!isValid) {
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            event.preventDefault();
            event.stopPropagation();

            // Focus first invalid field
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
            }
        }
    });
})();
