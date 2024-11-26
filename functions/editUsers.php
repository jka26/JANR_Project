<?php
include '../db/config.php'; // Include the database connection

// Get JSON input from fetch request
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id'], $data['name'], $data['age'], $data['gender'], $data['location'])) {
    $userId = $data['id'];
    $name = $data['name'];
    $age = $data['age'];
    $gender = $data['gender'];
    $location = $data['location'];

    // Prepare the SQL query to update the user
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, age = ?, gender = ?, location = ? WHERE user_id = ?");
    
    if ($stmt) {
        // Bind parameters (ensuring data types match the table schema)
        $stmt->bind_param("sisss", $name, $age, $gender, $location, $userId);

        // Execute the query and handle the result
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare the SQL statement']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input data']);
}

$conn->close();
?>
