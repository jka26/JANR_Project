<?php
include '../db/config.php';
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

$sender_id = intval($_SESSION['user_id']);
$receiver_id = intval($_GET['receiver_id']);
$last_time = isset($_GET['last_time']) ? $_GET['last_time'] : null;

try {
    $sql = $last_time 
        ? "SELECT m.chat_id, m.sender_id, m.receiver_id, m.message, m.sent_at, CONCAT(p.first_name, ' ', p.last_name) AS sender_name
           FROM messages m
           JOIN profiles p ON m.sender_id = p.user_id
           WHERE ((m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?))
           AND m.sent_at > ?
           ORDER BY m.sent_at ASC"
        : "SELECT m.chat_id, m.sender_id, m.receiver_id, m.message, m.sent_at, CONCAT(p.first_name, ' ', p.last_name) AS sender_name
           FROM messages m
           JOIN profiles p ON m.sender_id = p.user_id
           WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
           ORDER BY m.sent_at ASC";

    $stmt = $conn->prepare($sql);
    if ($last_time) {
        $stmt->bind_param("iiiss", $sender_id, $receiver_id, $receiver_id, $sender_id, $last_time);
    } else {
        $stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'chat_id' => $row['chat_id'],
            'sender_id' => $row['sender_id'],
            'receiver_id' => $row['receiver_id'],
            'message' => $row['message'],
            'sent_at' => $row['sent_at'],
            'sender_name' => $row['sender_name']
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $messages]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
