const clientBtn = document.getElementById("signup-client-btn");
const providerBtn = document.getElementById("signup-provider-btn");
const providerFields = document.getElementById("provider-fields");


const categoryInput = document.getElementById("serviceCategory");
const experienceInput = document.getElementById("experience");
const userTypeHidden = document.querySelector('input[name*="userType"]');

providerBtn.addEventListener("click", () => {
    providerBtn.classList.add("active");
    clientBtn.classList.remove("active");
    providerFields.classList.remove("hidden-section");

    if (userTypeHidden) userTypeHidden.value = "provider";

    if (categoryInput) categoryInput.required = true;
    if (experienceInput) experienceInput.required = true;
});



clientBtn.addEventListener("click", () => {

    clientBtn.classList.add("active");
    providerBtn.classList.remove("active");


    providerFields.classList.add("hidden-section");


    if (userTypeHidden) userTypeHidden.value = "client";

    if (categoryInput) categoryInput.required = false;
    if (experienceInput) experienceInput.required = false;
});


// Changed the password validation to make a better UI experience for users (Rayen)
const form = document.getElementById("advanced-signup-form");
const passwordInput = document.getElementById("registration_form_plainPassword");
const confirmPasswordInput = document.getElementById("confirmPassword");


if (form) {
    form.addEventListener("submit", (event) => {
      
        if (passwordInput.value !== confirmPasswordInput.value) {
           
            event.preventDefault(); 
            
            alert("Passwords do not match! Please check them and try again.");
            
          
            passwordInput.style.borderColor = "#e63946";
            confirmPasswordInput.style.borderColor = "#e63946";
        } else {

            passwordInput.style.borderColor = "";
            confirmPasswordInput.style.borderColor = "";
        }
    });
}