<?php
session_start();
include "../db/config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate POST inputs
    $firstName = isset($_POST['fname']) ? mysqli_real_escape_string($conn, $_POST['fname']) : null;
    $lastName = isset($_POST['lname']) ? mysqli_real_escape_string($conn, $_POST['lname']) : null;
    //$email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : null;
    //$password = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : null;
    $age = isset($_POST['age']) ? mysqli_real_escape_string($conn, $_POST['age']) : null;
    $location = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : null;
    $birthDate = isset($_POST['dob']) ? mysqli_real_escape_string($conn, $_POST['dob']) : null;
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : null;
    $personalBio = isset($_POST['bio']) ? mysqli_real_escape_string($conn, $_POST['bio']) : null;
    $hobbies = isset($_POST['hobbies']) ? mysqli_real_escape_string($conn, $_POST['hobbies']) : null;
    $personality = isset($_POST['personality']) ? mysqli_real_escape_string($conn, $_POST['personality']) : null;

    // Check for missing required fields
    if (!$firstName || !$lastName) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    // Hash the password for secure storage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Handle file upload
    $uploadDir = "uploads/";
    $profileImagePath = null; // Default to null if no file uploaded

    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        // Ensure upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Define the target path for the uploaded file
        $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
        $profileImagePath = $uploadDir . $fileName;

        // Validate file type and size
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($profileImagePath, PATHINFO_EXTENSION));
        $fileSize = $_FILES['profile_image']['size'];

        if (!in_array($fileType, $allowedTypes) || $fileSize > 5000000) { // Limit 5MB
            echo json_encode(['success' => false, 'message' => 'Invalid file type or file size exceeds 5MB.']);
            exit;
        }

        // Move the uploaded file
        if (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $profileImagePath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
            exit;
        }
    }

    $user_id = $_SESSION['user_id'];
    $checkUserSql = "SELECT user_id FROM profiles WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkUserSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // User already has a profile, return an error or update logic
        echo json_encode(['success' => false, 'message' => 'Profile already exists for this user.']);
        exit;
    } else {
        // Save user data to the database
        $sql = "INSERT INTO profiles (user_id, first_name, last_name, age, location, birth_date, gender, bio, hobbies, personality, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ississsssss",
            $user_id,
            $firstName,
            $lastName,
            //$email,
            //$hashedPassword,
            $age,
            $location,
            $birthDate,
            $gender,
            $personalBio,
            $hobbies,
            $personality,
            $profileImagePath
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $stmt->error]);
        }
    
        $stmt->close();
    }
    $checkStmt->close();   
    $conn->close();
}
?>
