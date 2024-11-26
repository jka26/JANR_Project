<?php
session_start();
include '../db/config.php'; // Include your MySQLi database connection

// Ensure the session variable for the user is set
if (!isset($_SESSION['user_id'])) {
    die("<p>Error: User is not logged in.</p>");
}

// Get the current user's ID from the session
$current_user_id = $_SESSION['user_id'];

try {
    // Query to fetch all users except the current user
    $sql = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS name, profile_image 
            FROM profiles
            WHERE user_id != ?";

    // Prepare the MySQLi statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<p>Error preparing statement: " . htmlspecialchars($conn->error) . "</p>");
    }

    // Bind the parameter and execute the statement
    $stmt->bind_param('i', $current_user_id);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->get_result();

    // Display each user
    while ($user = $result->fetch_assoc()) {
        echo "<div class='user' data-id='{$user['user_id']}'>";
        echo "<img src='{$user['profile_image']}' alt='Profile image of {$user['name']}'>";
        echo "<span class='name'>{$user['name']}</span>";
        echo "</div>";
    }
} catch (Exception $e) {
    // Handle any errors
    echo "<p>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
