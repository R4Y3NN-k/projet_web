document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('ajax-login-form');
    if (!loginForm) return;

    loginForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the full-page browser redirection

        const email = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const csrfToken = document.getElementById('csrf-token').value;

        let errorDiv = loginForm.previousElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('alert-danger')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger';
            errorDiv.style = 'color: #e63946; background: #ffe3e3; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;';
            loginForm.parentNode.insertBefore(errorDiv, loginForm);
        }

        errorDiv.style.display = 'none'; 
        
        // Use standard URL encoded form data format so FormLoginAuthenticator reads it perfectly
        const formData = new URLSearchParams();
        formData.append('_username', email);
        formData.append('_password', password);
        formData.append('_csrf_token', csrfToken);

        // Pointing directly back to your valid HTML login route path
        fetch('/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: formData.toString()
        })
        .then(response => {
            // If the server redirects (302) or loads a page, check where it went
            if (response.redirected) {
                // If it successfully validated, it will try to send you to the dashboard/home
                if (!response.url.includes('/login')) {
                    window.location.reload(); // Refresh where you stand to show logged in state
                    return;
                }
            }
            return response.text(); // Read the page content if it failed
        })
        .then(html => {
            if (!html) return;

            // Check if the resulting page contains a bootstrap danger alert from a bad password
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const serverError = doc.querySelector('.alert-danger');

            if (serverError) {
                // Bad credentials! Extract error text, display it in popup, keep popup open
                errorDiv.textContent = serverError.textContent.trim();
                errorDiv.style.display = 'block';
            } else {
                // Success fallback
                window.location.reload();
            }
        })
        .catch(error => {
            errorDiv.textContent = 'Invalid credentials or connection issue.';
            errorDiv.style.display = 'block';
        });
    });
});