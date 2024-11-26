<?php
include "../db/config.php"; // Ensure the database connection is properly configured

// Get user ID from the URL
$userId = $_GET['user_id'] ?? null;

if ($userId) {
    // Prepare the SQL query to fetch user details securely
    $sql = "SELECT * FROM profiles WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        } else {
            echo "User not found.";
            exit;
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement.";
        exit;
    }
} else {
    echo "Invalid user ID.";
    exit;
}

$conn->close();
?>