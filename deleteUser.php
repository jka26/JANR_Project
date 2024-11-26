<?php
include "../db/config.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id'])) {
        $user_id = $data['user_id'];

        $sql = "DELETE FROM profiles WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "User deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error deleting user: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to prepare the SQL statement."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid user ID."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method."]);
}

$conn->close();
?>
