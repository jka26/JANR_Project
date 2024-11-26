
        document.getElementById("loginForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form submission
            
            //Clear previous error messages
            document.getElementById("emailError").style.display = 'none';
            document.getElementById("passwordError").style.display = 'none';
            document.getElementById("errorMessage").innerText = '';


            // Validate email format
            const email = document.getElementById("email").value;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                document.getElementById("emailError").style.display = 'block';
                return;
            }

            // Validate password
            const password = document.getElementById("password").value;
            let hasError = false;

            if (password.length < 8) {
                document.getElementById("passwordError").innerText = "Password must be at least 8 characters long.";
                document.getElementById("passwordError").style.display = 'block';
                hasError = true;
            }
            if (!/[A-Z]/.test(password)) {
                document.getElementById("passwordError").innerText += "\nPassword must contain at least one uppercase letter.";
                document.getElementById("passwordError").style.display = 'block';
                hasError = true;
            }
            if ((password.match(/\d/g) || []).length < 3) {
                document.getElementById("passwordError").innerText += "\nPassword must include at least three digits.";
                document.getElementById("passwordError").style.display = 'block';
                hasError = true;
            }
            if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                document.getElementById("passwordError").innerText += "\nPassword must contain at least one special character.";
                document.getElementById("passwordError").style.display = 'block';
                hasError = true;
            }

            // If there are no errors, proceed with form submission (here, we just log to console)
            if (!hasError) {
                console.log("Form submitted successfully!");
                // Here you can proceed with the actual form submission (e.g., send data to server)
            }
        });