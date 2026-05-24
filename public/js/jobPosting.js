document.addEventListener('DOMContentLoaded', () => {
            const jobModal = document.getElementById('job-modal');
            const openBtn = document.querySelector('.btn-primary'); // Make sure your "+ Post a New Job" button has this class or an ID!
            const closeBtn = document.getElementById('close-job-modal');

            // Open Modal
            if (openBtn && jobModal) {
                openBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    jobModal.style.display = 'flex';
                });
            }

            // Close Modal via X button
            if (closeBtn && jobModal) {
                closeBtn.addEventListener('click', () => {
                    jobModal.style.display = 'none';
                });
            }

            // Close Modal by clicking outside
            window.addEventListener('click', (e) => {
                if (e.target === jobModal) {
                    jobModal.style.display = 'none';
                }
            });
            
            // Close Modal with Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && jobModal.style.display === 'flex') {
                    jobModal.style.display = 'none';
                }
            });
        });