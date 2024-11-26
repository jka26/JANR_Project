document.getElementById('signup-form').addEventListener('submit', function(event) {
    event.preventDefault(); 
  
    const firstName = document.getElementById('first-name').value;
    const lastName = document.getElementById('last-name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
  
    document.getElementById('first-name-error').textContent = '';
    document.getElementById('last-name-error').textContent = '';
    document.getElementById('email-error').textContent = '';
    document.getElementById('password-error').textContent = '';
    document.getElementById('confirm-password-error').textContent = '';
  
    let isValid = true; 
  
    // First name validation
    if (!firstName) {
      document.getElementById('first-name-error').textContent = 'First name is required.';
      isValid = false;
    }
  
    // Last name validation
    if (!lastName) {
      document.getElementById('last-name-error').textContent = 'Last name is required.';
      isValid = false;
    }
  
    // Email validation
    const emailRegex = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
    if (!email) {
      document.getElementById('email-error').textContent = 'Email is required.';
      isValid = false;
    } else if (!emailRegex.test(email)) {
      document.getElementById('email-error').textContent = 'Please enter a valid email address.';
      document.getElementById("email-div").style.cssText = 'border-style: solid;border-color: red;padding: 5px';
      isValid = false;
    } else {
      document.getElementById("email-div").style.cssText = 'border-style: solid;border-color: green;padding: 5px';
    }
  
    // Password validation
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    if (!password) {
      document.getElementById('password-error').textContent = 'Password is required.';
      isValid = false;
    } else if (!passwordRegex.test(password)) {
      document.getElementById('password-error').textContent = 'Password must be at least 8 characters long, contain at least one uppercase letter, a digit, and at least one special character.';
      document.getElementById("pass-div").style.cssText = 'border-style: solid;border-color: red;padding: 5px';
      isValid = false;
    } else {
      document.getElementById("pass-div").style.cssText = 'border-style: solid;border-color: green;padding: 5px';
    }
  
    // Confirm password validation
    if (!confirmPassword) {
      document.getElementById('confirm-password-error').textContent = 'Please confirm your password.';
      isValid = false;
    } else if (password !== confirmPassword) {
      document.getElementById('confirm-password-error').textContent = 'Passwords do not match.';
      isValid = false;
    }
  
    if (isValid) {
      alert('Sign up successful!');
    }
});

  