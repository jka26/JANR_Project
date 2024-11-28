<?php
include "../db/config.php";
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['user_id'], $data['fname'], $data['lname'], $data['age'], $data['gender'], $data['location'])) {
        $user_id = $data['user_id'];
        $fname = $data['fname'];
        $lname = $data['lname'];
        $age = $data['age'];
        $gender = $data['gender'];
        $location = $data['location'];

        $sql = "UPDATE profiles SET first_name = ?, last_name = ?, age = ?, gender = ?, location = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ssissi", $fname, $lname, $age, $gender, $location, $user_id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "User details updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Error updating user: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to prepare the SQL statement."]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid data provided."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["message" => "Invalid request method."]);
}

$conn->close();
?>
