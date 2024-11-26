<?php
// Disable error display, but log errors for debugging
ini_set('display_errors', 0); 
ini_set('log_errors', 1);    
error_log('Error in fetch_messages.php'); 

header('Content-Type: application/json'); // Ensure JSON output

include "../db/config.php"; // Database connection

// Validate receiver_id is present in the request
if (!isset($_GET['receiver_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing receiver_id"]);
    exit;
}

$receiver_id = intval($_GET['receiver_id']);

// Ensure the sender's ID is available in session (i.e., logged-in user)
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$current_user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Query to fetch chat messages between the logged-in user and the selected receiver
$query = $conn->prepare("SELECT messages.sender_id, messages.message, messages.created_at, users.first_name, users.last_name 
                        FROM messages 
                        INNER JOIN users ON messages.sender_id = users.user_id 
                        WHERE (messages.sender_id = ? AND messages.receiver_id = ?) 
                        OR (messages.sender_id = ? AND messages.receiver_id = ?) 
                        ORDER BY messages.created_at ASC");

$query->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $messages = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format the message output
        $messages[] = [
            "sender" => ($row['sender_id'] == $current_user_id) ? "You" : $row['first_name'] . " " . $row['last_name'],
            "text" => htmlspecialchars($row['message']), // Escape message text for safety
            "created_at" => $row['created_at']
        ];
    }

    echo json_encode(["status" => "success", "data" => $messages]);
} else {
    echo json_encode(["status" => "error", "message" => "No messages found"]);
}

$query->close();
$conn->close();
?>
