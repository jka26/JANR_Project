<?php
include "../db/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and retrieve form data
    $first_name = trim($_POST['first-name']);
    $last_name = trim($_POST['last-name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $role = 2;

    // Initialize an errors array
    $errors = [];

    // Simple validation
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Validate email format and check for duplicates
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT user_id FROM profiles WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email is already registered.";
        }
        $stmt->close();
    }

    // Validate password
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }
    if (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter.";
    }
    if (!preg_match("/\d/", $password)) {
        $errors[] = "Password must include at least one digit.";
    }
    if (!preg_match("/[@$!%*#?&]/", $password)) {
        $errors[] = "Password must contain at least one special character.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if there are any validation errors
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user into database using a prepared statement
        $stmt = $conn->prepare("INSERT INTO profiles (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $first_name, $last_name, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Registration successful!']);
            exit();
        } else {
            echo json_encode(['success' => false, 'errors' => ["Error: " . $stmt->error]]);
            exit();
        }
        $stmt->close();
    } else {
        // Return errors as JSON
        echo json_encode(['success' => false, 'errors' => $errors]);
        exit();
    }

    $conn->close();
}
