<?php
include "../db/config.php"; // Ensure the database connection file is included

// Get user ID from URL
$userId = $_GET['user_id'] ?? null;

if ($userId) {
    // Prepare the SQL query to delete the user
    $sql = "DELETE FROM profiles WHERE user_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $userId);

        // Execute the query and handle the result
        if ($stmt->execute()) {
            // Redirect to user management page with a success message
            header("Location: usermanagement.php?message=User deleted successfully");
            exit;
        } else {
            echo "Error deleting user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Failed to prepare the SQL statement.";
    }
} else {
    echo "Invalid user ID.";
}

$conn->close();
?>
