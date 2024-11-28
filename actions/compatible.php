<?php
session_start();
include "../db/config.php";

// Output HTML structure at the beginning
echo '<!DOCTYPE html>
<html>
<head>
    <style>
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        .compatibility-score {
            justify-content: center;
            color: violet;
            display: flex;
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <canvas id="confettiCanvas"></canvas>';

// Check if the form is submitted
if (isset($_POST['connect_button'])) {
    $user_id = $_SESSION['user_id'];
    $selected_user_id = $_POST['selected_user_id'];
    $compatibility_score = calculateCompatibility($user_id, $selected_user_id, $conn);
    echo "<h1 class='compatibility-score'>Compatibility Score: $compatibility_score</h1>";
    echo "<script>window.compatibilityScore = $compatibility_score;</script>";
}

function calculateCompatibility($user_id, $selected_user_id, $conn) {
    // Fetch user data (age, gender)
    $userQuery = $conn->prepare("SELECT age, gender FROM profiles WHERE user_id = ?");
    $userQuery->bind_param("i", $user_id);
    $userQuery->execute();
    $userData = $userQuery->get_result()->fetch_assoc();
    if (!$userData) {
        echo "Error: User data not found.";
        return 0;
    }
    
    // Fetch selected user's data (age, gender)
    $selectedUserQuery = $conn->prepare("SELECT age, gender FROM profiles WHERE user_id = ?");
    $selectedUserQuery->bind_param("i", $selected_user_id);
    $selectedUserQuery->execute();
    $selectedUserData = $selectedUserQuery->get_result()->fetch_assoc();
    if (!$selectedUserData) {
        echo "Error: Selected user's data not found.";
        return 0;
    }
    
    // Fetch user's preferences
    $prefQuery = $conn->prepare("SELECT preferred_gender, min_age, max_age FROM profiles WHERE user_id = ?");
    $prefQuery->bind_param("i", $user_id);
    $prefQuery->execute();
    $preferences = $prefQuery->get_result()->fetch_assoc();
    if (!$preferences) {
        echo "Error: User preferences not found.";
        return 0;
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

// Add the confetti JavaScript at the end
echo '<script>
document.addEventListener("DOMContentLoaded", function() {
    const canvas = document.getElementById("confettiCanvas");
    const ctx = canvas.getContext("2d");

    // Set canvas size
    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    resizeCanvas();
    window.addEventListener("resize", resizeCanvas);

    class Confetti {
        constructor() {
            this.reset();
        }

        reset() {
            this.x = Math.random() * canvas.width;
            this.y = -10;
            this.width = Math.random() * 10 + 5;
            this.height = Math.random() * 6 + 4;
            this.speed = Math.random() * 3 + 2;
            this.angle = Math.random() * 360;
            this.spin = Math.random() * 8 - 4;
            this.color = this.getRandomPinkShade();
            this.opacity = 1;
        }

        getRandomPinkShade() {
            const pinks = [
                "rgba(255, 182, 193, 0.9)", // Light pink
                "rgba(255, 105, 180, 0.9)", // Hot pink
                "rgba(255, 192, 203, 0.9)", // Pink
                "rgba(255, 228, 225, 0.9)", // Misty rose
                "rgba(219, 112, 147, 0.9)"  // Pale violet red
            ];
            return pinks[Math.floor(Math.random() * pinks.length)];
        }

        update() {
            this.y += this.speed;
            this.angle += this.spin;
            this.opacity -= 0.005;

            if (this.y > canvas.height || this.opacity <= 0) {
                this.reset();
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.angle * Math.PI / 180);
            ctx.globalAlpha = this.opacity;
            ctx.fillStyle = this.color;
            ctx.fillRect(-this.width / 2, -this.height / 2, this.width, this.height);
            ctx.restore();
        }
    }

    // Create confetti particles
    const particleCount = Math.min((window.compatibilityScore || 0) * 10, 150);
    const particles = Array(particleCount).fill().map(() => new Confetti());

    // Animation loop
    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(particle => {
            particle.update();
            particle.draw();
        });

        requestAnimationFrame(animate);
    }

    // Start animation if there is a compatibility score
    if (window.compatibilityScore) {
        animate();
    }
});
</script>
</body>
</html>';
?>