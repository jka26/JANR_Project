<?php
include '../db/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id']) || !isset($_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$sender_id = intval($_SESSION['user_id']);
$receiver_id = intval($_POST['receiver_id']);
$message = trim($_POST['message']);

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty']);
    exit;
}

try {
    $sql = "INSERT INTO messages (sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
    } else {
        throw new Exception('Failed to send message');
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
