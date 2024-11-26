<?php
session_start();
include "../db/config.php";

// Check if the form is submitted
if (isset($_POST['connect_button'])) {
    $user_id = $_SESSION['user_id']; // Assuming the user ID is stored in the session
    $selected_user_id = $_POST['selected_user_id'];

    $compatibility_score = calculateCompatibility($user_id, $selected_user_id, $conn);

    // Store compatibility score in session and redirect
    $_SESSION['compatibility_score'] = $compatibility_score;
    header("Location: ../actions/compatible.php");
    exit;
}

function calculateCompatibility($user_id, $selected_user_id, $conn) {
    // Fetch user data (age, gender)
    $userQuery = $conn->prepare("SELECT age, gender FROM profiles WHERE user_id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userData = $userQuery->get_result()->fetch_assoc();

    if (!$userData) {
        echo "Error: User data not found.";
        return 0; // Default compatibility score
    }

    // Fetch selected user's data (age, gender)
    $selectedUserQuery = $conn->prepare("SELECT age, gender FROM profiles WHERE user_id = ?");
    $selectedUserQuery->bind_param("i", $selected_user_id);
    $selectedUserQuery->execute();
    $selectedUserData = $selectedUserQuery->get_result()->fetch_assoc();

    if (!$selectedUserData) {
        echo "Error: Selected user's data not found.";
        return 0; // Default compatibility score
    }

    // Fetch user's preferences
    $prefQuery = $conn->prepare("SELECT preferred_gender, min_age, max_age FROM preferences WHERE user_id = ?");
    $prefQuery->bind_param("i", $user_id);
    $prefQuery->execute();
    $preferences = $prefQuery->get_result()->fetch_assoc();

    if (!$preferences) {
        echo "Error: User preferences not found.";
        return 0; // Default compatibility score
    }

    // Initialize compatibility score
    $score = 0;

    // Age Compatibility
    if ($selectedUserData['age'] >= $preferences['min_age'] && $selectedUserData['age'] <= $preferences['max_age']) {
        $score += 10;
    }

    // Gender Compatibility
    if ($selectedUserData['gender'] == $preferences['preferred_gender']) {
        $score += 10;
    }

    // Store or Update Compatibility Score in Matches Table
    $matchQuery = $conn->prepare("
        INSERT INTO matches (user_id, matched_user_id, compatibility_score)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE compatibility_score = ?
    ");
    $matchQuery->bind_param("iiii", $user_id, $selected_user_id, $score, $score);
    $matchQuery->execute();

    return $score;
}
?>
