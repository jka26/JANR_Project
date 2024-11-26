<?php
ini_set('display_errors', 0); // Turn off error display
ini_set('log_errors', 1);    // Log errors instead
error_log('Error in fetch_messages.php'); // Save error details to log file

header('Content-Type: application/json'); // Ensure JSON output

include "../db/config.php"; // Database connection

if (!isset($_GET['receiver_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing receiver_id"]);
    exit;
}

$receiver_id = intval($_GET['receiver_id']);
$current_user_id = 1; // Replace this with the logged-in user's ID dynamically

$query = $db->prepare("SELECT * FROM messages WHERE 
                        (sender_id = ? AND receiver_id = ?) OR 
                        (sender_id = ? AND receiver_id = ?) 
                        ORDER BY created_at ASC");
$query->bind_param("iiii", $current_user_id, $receiver_id, $receiver_id, $current_user_id);
$query->execute();
$result = $query->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        "sender" => ($row['sender_id'] == $current_user_id) ? "You" : "Them",
        "text" => htmlspecialchars($row['message']), // Escape for safety
    ];
}

echo json_encode(["status" => "success", "data" => $messages]);
exit;
?>
