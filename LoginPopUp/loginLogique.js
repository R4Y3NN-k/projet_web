const loginBtn = document.getElementById("login-btn");
const loginModal = document.getElementById("login-modal");


loginBtn.addEventListener("click", () => {
    loginModal.classList.add("active");
});


loginModal.addEventListener("click", (event) => {

    if (event.target === loginModal) {
        loginModal.classList.remove("active");
    }
});