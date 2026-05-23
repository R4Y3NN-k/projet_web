document.addEventListener('DOMContentLoaded', () => {
    const loginModal = document.getElementById('login-modal');
    const loginTriggers = document.querySelectorAll('#login-btn, .login-trigger');

    // Safety check: if the modal isn't on the page, don't run the rest of the script
    if (!loginModal) return;

    // ==========================================
    // 1. FORCE OPEN ON ERROR (Symfony feedback)
    // ==========================================
    // If Symfony printed the error box, immediately show the modal
    if (document.querySelector('.alert-danger')) {
        loginModal.style.display = 'flex';
        loginModal.style.opacity = '1';
        loginModal.style.visibility = 'visible';
    }

    // ==========================================
    // 2. OPEN MODAL (Clicking the Nav Button)
    // ==========================================
    loginTriggers.forEach(trigger => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault(); 
            loginModal.style.display = 'flex'; 
            loginModal.style.opacity = '1';
            loginModal.style.visibility = 'visible';
        });
    });

    // ==========================================
    // 3. CLOSE MODAL (Clicking outside)
    // ==========================================
    loginModal.addEventListener('click', (e) => {
        if (e.target === loginModal) {
            loginModal.style.display = 'none';
        }
    });

    // ==========================================
    // 4. CLOSE MODAL (Pressing Escape)
    // ==========================================
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && loginModal.style.display !== 'none') {
            loginModal.style.display = 'none';
        }
    });
});