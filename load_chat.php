<?php
session_start();
include "config.php";

$current_user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'] ?? 0; // User you're chatting with

$sql = "SELECT * FROM messages 
        WHERE (sender_id = ? AND receiver_id = ?)
           OR (sender_id = ? AND receiver_id = ?) 
        ORDER BY created_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

header("Content-Type: application/json");
echo json_encode($messages);
?>
