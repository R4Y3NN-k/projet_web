document.addEventListener('DOMContentLoaded', () => {
    // Grab the modal and form
    const loginModal = document.getElementById('login-modal');
    const loginForm = loginModal ? loginModal.querySelector('form') : null;
    
    // Grab ALL buttons or links that should open the modal
    // We are selecting the old ID just in case, plus the new class!
    const loginTriggers = document.querySelectorAll('#login-btn, .login-trigger');

    // 1. OPEN MODAL: Loop through all triggers and add the click event
    if (loginModal) {
        loginTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault(); // Stop links from jumping to top of page
                loginModal.style.display = 'flex'; 
                loginModal.style.opacity = '1';
                loginModal.style.visibility = 'visible';
            });
        });

        // 2. CLOSE MODAL: Click outside the modal content
        loginModal.addEventListener('click', (e) => {
            if (e.target === loginModal) {
                loginModal.style.display = 'none';
            }
        });
    }

    // 3. CLOSE MODAL: Pressing the Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && loginModal && loginModal.style.display !== 'none') {
            loginModal.style.display = 'none';
        }
    });

    // // 4. FORM SUBMISSION: Handle the login attempt
    // if (loginForm) {
    //     loginForm.addEventListener('submit', (e) => {
    //         e.preventDefault(); 
    //         const email = loginForm.querySelector('input[type="email"]').value;
    //         console.log(`Attempting login for: ${email}`);
    //         alert('Frontend logic is working! Symfony backend connection coming soon.');
            
    //         loginForm.reset();
    //         loginModal.style.display = 'none';
    //     });
    // }
    document.addEventListener("DOMContentLoaded", function() {
    const loginModal = document.getElementById('login-modal');
    
    // IF Symfony returns an error alert, force the modal to stay visible 
    if (document.querySelector('.alert-danger')) {
        loginModal.style.display = 'flex'; 
    }
    
    // ... your remaining click listeners to open/close the modal ...
});
});