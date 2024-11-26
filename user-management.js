// Get the modal
const modal = document.getElementById("addUserModal");

// Get the button that opens the modal
const addUserBtn = document.querySelector("button[onclick='showAddUserForm()']");

// Get the <span> element that closes the modal
const closeSpan = document.getElementsByClassName("close")[0];

// Get the form and error message elements
const addUserForm = document.getElementById("addUserForm");
const emailInput = document.getElementById("email");
const emailError = document.getElementById("emailError");

// Function to show the modal
function showAddUserForm() {
    modal.style.display = "block";
}

// Function to hide the modal
function hideModal() {
    modal.style.display = "none";
    emailError.style.display = "none";
    addUserForm.reset();
}

// When the user clicks on the button, open the modal
addUserBtn.onclick = showAddUserForm;

// When the user clicks on <span> (x), close the modal
closeSpan.onclick = hideModal;

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        hideModal();
    }
}

// Function to validate email
function validateEmail(email) {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(String(email).toLowerCase());
}

// Handle form submission
addUserForm.onsubmit = function(e) {
    e.preventDefault();
    const email = emailInput.value;

    if (!validateEmail(email)) {
        emailError.style.display = "block";
        return;
    }

    // If email is valid, you can proceed with adding the user
    // For now, we'll just log the data and close the modal
    console.log("Adding user:", {
        name: document.getElementById("name").value,
        email: email
    });

    hideModal();
};

// Real-time email validation
emailInput.addEventListener("input", function() {
    if (validateEmail(this.value)) {
        emailError.style.display = "none";
    } else {
        emailError.style.display = "block";
    }
});
