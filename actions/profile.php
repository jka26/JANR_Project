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
    $preferred_gender = isset($_POST['preferred_gender']) ? mysqli_real_escape_string($conn, $_POST['preferred_gender']) : null;
    $min_age = isset($_POST['min_age']) ? mysqli_real_escape_string($conn, $_POST['min_age']) : null;
    $max_age= isset($_POST['max_age']) ? mysqli_real_escape_string($conn, $_POST['max_age']) : null;

    // Check for missing required fields
    if (!$firstName || !$lastName) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
        exit;
    }

    // Hash the password for secure storage
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);


    // Handle file upload
$uploadDir = "uploads/";
$profileImagePath = null;

if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get file details
    $tmpName = $_FILES['profile_image']['tmp_name'];
    $originalName = basename($_FILES['profile_image']['name']);
    $fileSize = $_FILES['profile_image']['size'];

    // Get actual file type using mime type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tmpName);
    finfo_close($finfo);

    // Allowed mime types
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif'
    ];

    // Validate file type and size
    if (!array_key_exists($mimeType, $allowedMimeTypes) || $fileSize > 5000000) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type or file size exceeds 5MB.']);
        exit;
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . $originalName;
    $profileImagePath = $uploadDir . $fileName;

    // Move uploaded file with additional security checks
    if (is_uploaded_file($tmpName) && move_uploaded_file($tmpName, $profileImagePath)) {
        // File upload successful
        echo json_encode(['success' => true, 'message' => 'File uploaded successfully.', 'path' => $profileImagePath]);
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed.']);
        exit;
    }
} else {
    // Handle upload errors
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];

    $errorCode = $_FILES['profile_image']['error'] ?? UPLOAD_ERR_NO_FILE;
    echo json_encode([
        'success' => false, 
        'message' => $errorMessages[$errorCode] ?? 'Unknown upload error'
    ]);
    exit;
}

    $user_id = $_SESSION['user_id'];

    // Check if the user already has a profile
    $checkUserSql = "SELECT user_id FROM profiles WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkUserSql);
    $checkStmt->bind_param("i", $user_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // User already has a profile, update it
        $sql = "UPDATE profiles 
                    SET first_name = ?, 
                        last_name = ?, 
                        age = ?, 
                        location = ?, 
                        birth_date = ?, 
                        gender = ?, 
                        bio = ?, 
                        hobbies = ?, 
                        personality = ?, 
                        profile_image = ?, 
                        preferred_gender = ?, 
                        min_age = ?, 
                        max_age = ? 
                    WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssissssssssiii",
            $firstName,
            $lastName,
            $age,
            $location,
            $birthDate,
            $gender,
            $personalBio,
            $hobbies,
            $personality,
            $profileImagePath,
            $preferred_gender,
            $min_age,
            $max_age,
            $user_id
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Profile created successfully!']);
            header("Location: ../view/matchmaking.php");
        } else {
            echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $stmt->error]);
        }
    
        $stmt->close();
    }
    $checkStmt->close();   
    $conn->close();
}
?>
