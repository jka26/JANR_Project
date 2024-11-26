<?php
include '../db/config.php';

// Enable JSON response and error reporting for debugging
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
if (!isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: receiver_id or message'
    ]);
    exit;
}

$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (empty($message)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Message cannot be empty'
    ]);
    exit;
}

// Ensure the sender ID is set via session
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in'
    ]);
    exit;
}

$sender_id = intval($_SESSION['user_id']);

// Insert the message into the database
try {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to prepare SQL statement'
        ]);
        exit;
    }

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
            'message' => 'Failed to send message. Please try again.'
        ]);
    }

    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}

$conn->close();
?>
