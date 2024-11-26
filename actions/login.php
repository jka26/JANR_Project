<?php
session_start();

include "config.php";

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST["password"]);
    $errors = [];

    // Server-side validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // If validation passed
    if (empty($errors)) {
        // Verify user credentials
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $first_name, $last_name, $email, $hashed_password);
            $stmt->fetch();

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Start session and store user data
                $_SESSION['user_id'] = $user_id;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                //$_SESSION['role'] = $role;

                // Redirect based on role
                // if ($role == 1) { // Super Admin role
                //     header("Location: dashboard.php");
                //     exit();
                // } elseif ($role == 2) { // Regular Admin role
                //     header("Location: dashboard.php");
                //     exit();
                // } else {
                //     header("Location: dashboard.php");
                // }
                // exit;
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with this email.";
        }
        $stmt->close();
    }

    // Handle errors
    if (!empty($errors)) {
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
    $conn->close();
}
?>
