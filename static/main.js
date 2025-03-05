// main.js
document.addEventListener('DOMContentLoaded', function() {
    // Get all necessary DOM elements
    const roleOptions = document.querySelectorAll('.role-option');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const toggleFormBtn = document.getElementById('toggleForm');
    const adminFields = document.getElementById('adminSpecificFields');
    const institutionFields = document.getElementById('institutionFields');
    const signupAdminFields = document.getElementById('signupAdminFields');
    const signupInstitutionFields = document.getElementById('signupInstitutionFields');
    const selectedRoleInput = document.getElementById('selectedRole');

    let isLoginForm = true;
    let currentRole = null;

    // Initialize forms
    loginForm.classList.remove('hidden');
    signupForm.classList.add('hidden');

    // Role selection functionality
    roleOptions.forEach(option => {
        option.addEventListener('click', function() {
            const role = this.dataset.role;
            currentRole = role;
            
            // Update hidden role input
            if(selectedRoleInput) {
                selectedRoleInput.value = role;
            }

            // Reset all options
            roleOptions.forEach(opt => {
                const div = opt.querySelector('div');
                div.classList.remove('border-blue-500', 'bg-blue-50');
                const radio = opt.querySelector('input[type="radio"]');
                if(radio) radio.checked = false;
            });

            // Select current option
            const currentDiv = this.querySelector('div');
            const currentRadio = this.querySelector('input[type="radio"]');
            if(currentDiv && currentRadio) {
                currentDiv.classList.add('border-blue-500', 'bg-blue-50');
                currentRadio.checked = true;
            }

            showRelevantFields(role);
        });
    });

    // Form toggle functionality
    toggleFormBtn.addEventListener('click', function() {
        isLoginForm = !isLoginForm;
        if (isLoginForm) {
            loginForm.classList.remove('hidden');
            signupForm.classList.add('hidden');
            this.textContent = "Don't have an account? Sign up";
        } else {
            loginForm.classList.add('hidden');
            signupForm.classList.remove('hidden');
            this.textContent = 'Already have an account? Login';
        }

        // Maintain selected role fields
        if (currentRole) {
            showRelevantFields(currentRole);
        }
    });

    // Password toggle functionality
    const togglePassword = document.getElementById('togglePassword');
    const toggleSignupPassword = document.getElementById('toggleSignupPassword');
    
    [togglePassword, toggleSignupPassword].forEach(toggle => {
        if(toggle) {
            toggle.addEventListener('click', function() {
                const passwordInput = this.parentElement.querySelector('input[type="password"]');
                if(passwordInput) {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    // Update toggle button icon (optional)
                    const svg = this.querySelector('svg');
                    if(svg) {
                        if(type === 'password') {
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                        } else {
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
                        }
                    }
                }
            });
        }
    });

    // Password validation functionality
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    const requirements = {
        length: { regex: /.{8,}/, text: 'At least 8 characters' },
        uppercase: { regex: /[A-Z]/, text: 'One uppercase letter' },
        number: { regex: /[0-9]/, text: 'One number' },
        special: { regex: /[!@#$%^&*]/, text: 'One special character' }
    };

    passwordInputs.forEach(input => {
        input.addEventListener('input', function() {
            validatePassword(this.value);
        });
    });

    function validatePassword(password) {
        const validationResults = {};
        Object.keys(requirements).forEach(req => {
            validationResults[req] = requirements[req].regex.test(password);
        });

        // Update UI checkmarks
        Object.entries(validationResults).forEach(([requirement, isValid]) => {
            const reqElement = document.querySelector(`li:contains("${requirements[requirement].text}")`);
            if(reqElement) {
                const checkmark = reqElement.querySelector('span');
                if(checkmark) {
                    checkmark.textContent = isValid ? '✓' : '✗';
                    checkmark.style.color = isValid ? '#10B981' : '#EF4444';
                }
            }
        });

        return Object.values(validationResults).every(Boolean);
    }

    // Form submission handling
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate role selection
            if (!currentRole) {
                alert('Please select a role');
                return;
            }

            // Validate password if it's signup form
            if (!isLoginForm) {
                const password = this.querySelector('input[name="password"]').value;
                if (!validatePassword(password)) {
                    alert('Please meet all password requirements');
                    return;
                }
            }

            // If all validations pass, submit the form
            this.submit();
        });
    });

    // Function to show/hide relevant fields based on role
    function showRelevantFields(role) {
        // Hide all fields first
        [adminFields, institutionFields, signupAdminFields, signupInstitutionFields].forEach(
            field => {
                if(field) field.classList.add('hidden');
            }
        );

        // Show relevant fields based on role
        if (role === 'administrator') {
            if(adminFields) adminFields.classList.remove('hidden');
            if(signupAdminFields) signupAdminFields.classList.remove('hidden');
        } else if (['college', 'school'].includes(role)) {
            if(institutionFields) institutionFields.classList.remove('hidden');
            if(signupInstitutionFields) signupInstitutionFields.classList.remove('hidden');
        }
    }

    // Custom selector for contains text
    jQuery.expr[':'].contains = function(a, i, m) {
        return jQuery(a).text().includes(m[3]);
    };

    // Handle error messages if any
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    if(error) {
        alert(decodeURIComponent(error));
    }
});