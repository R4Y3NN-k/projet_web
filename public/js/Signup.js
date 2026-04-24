const clientBtn = document.getElementById("signup-client-btn");
const providerBtn = document.getElementById("signup-provider-btn");
const providerFields = document.getElementById("provider-fields");


const categoryInput = document.getElementById("serviceCategory");
const experienceInput = document.getElementById("experience");


providerBtn.addEventListener("click", () => {

    providerBtn.classList.add("active");
    clientBtn.classList.remove("active");


    providerFields.classList.remove("hidden-section");


    categoryInput.required = true;
    experienceInput.required = true;
});


clientBtn.addEventListener("click", () => {

    clientBtn.classList.add("active");
    providerBtn.classList.remove("active");


    providerFields.classList.add("hidden-section");


    categoryInput.required = false;
    experienceInput.required = false;
});