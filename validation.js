document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("registerForm").addEventListener("submit", function (e) {
        let password = document.getElementById("password").value;
        let contact = document.getElementById("contact").value;
        let errorMsg = "";

        // Validate password strength
        let strongPassword = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        if (!strongPassword.test(password)) {
            errorMsg += "Password must be at least 8 characters, contain an uppercase letter, a number, and a special character.<br>";
        }

        // Validate contact number
        if (!/^\d{10}$/.test(contact)) {
            errorMsg += "Contact number must be exactly 10 digits.<br>";
        }

        if (errorMsg !== "") {
            document.getElementById("errorMessages").innerHTML = "<div style='color:red;'>" + errorMsg + "</div>";
            e.preventDefault();
        }
    });
});
