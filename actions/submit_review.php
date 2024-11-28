<?php
// Start session to access user data
session_start();

// Database connection
include "../db/config.php";

// Validate input and ensure user session is active
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; // Get the logged-in user's ID from the session
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['comment']);

    // Basic validation
    if (empty($review_text) || $rating < 1 || $rating > 5) {
        echo "Invalid input.";
        exit;
    }

    // Prepared statement to avoid SQL injection
    $stmt = $conn->prepare("INSERT INTO reviews (user_id, review_content, rating) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $review_text, $rating);

    // Execute and check for errors
    if ($stmt->execute()) {
        echo "Review submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: User not logged in or invalid request.";
}

$conn->close();
?>
