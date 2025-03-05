// auth.js
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.querySelector('#loginForm form');
    const signupForm = document.querySelector('#signupForm form');

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const role = document.querySelector('input[name="role"]:checked').value;
        const email = loginForm.querySelector('input[type="email"]').value;
        const password = loginForm.querySelector('input[type="password"]').value;

        try {
            const response = await fetch('http://your-backend-url/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'login',
                    role: role,
                    email: email,
                    password: password
                })
            });

            const result = await response.json();
            
            if (result.success) {
                // Store auth token or user info in localStorage
                localStorage.setItem('user', JSON.stringify({email, role}));
                window.location.href = 'dashboard.html'; // Redirect to dashboard
            } else {
                alert(result.error);
            }
        } catch (error) {
            console.error('Login error:', error);
            alert('An error occurred during login');
        }
    });

    signupForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const role = document.querySelector('input[name="role"]:checked').value;
        const email = signupForm.querySelector('input[type="email"]').value;
        const password = signupForm.querySelector('input[type="password"]').value;
        
        const additionalData = role === 'administrator' ? {
            first_name: signupForm.querySelector('input[name="first_name"]').value,
            last_name: signupForm.querySelector('input[name="last_name"]').value,
            admin_code: signupForm.querySelector('input[name="admin_code"]').value,
            department: signupForm.querySelector('select[name="department"]').value
        } : {
            first_name: signupForm.querySelector('input[name="first_name"]').value,
            last_name: signupForm.querySelector('input[name="last_name"]').value,
            institution_name: signupForm.querySelector('input[name="institution_name"]').value,
            institution_type: signupForm.querySelector('select[name="institution_type"]').value,
            address: signupForm.querySelector('textarea[name="address"]').value
        };

        try {
            const response = await fetch('http://your-backend-url/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'signup',
                    role: role,
                    email: email,
                    password: password,
                    additional_data: additionalData
                })
            });

            const result = await response.json();
            
            if (result.success) {
                alert('Registration successful! Please login.');
                // Toggle to login form
                document.getElementById('toggleForm').click();
            } else {
                alert(result.errors ? result.errors.join('\n') : 'Registration failed');
            }
        } catch (error) {
            console.error('Signup error:', error);
            alert('An error occurred during signup');
        }
    });
});