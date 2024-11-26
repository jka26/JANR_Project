<?php

include '../db/config.php';

// Enable JSON response and error reporting for debugging
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
if (isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $receiver_id = $_POST['receiver_id'];
    $message = $_POST['message'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: receiver_id or message'
    ]);
    exit;
}

// Assuming the sender ID is set from session (update as needed)
session_start();
if (isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

// Insert message into the database
try {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Message sent successfully'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send message'
        ]);
    }

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>
