function validateForm() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const errorDiv = document.getElementById("error-message");

    if (password !== confirmPassword) {
        errorDiv.style.display = "block";
        errorDiv.innerText = "Error: Passwords do not match";
        return false;
    } else {
        return true;
    }
}