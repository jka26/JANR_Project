<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matchmaker - Find Your Match</title>
  <link rel="icon" href="../assets/favicon_rush.ico" type="ico">
  <link rel="stylesheet" href="../assets/matchmaking_style.css">
</head>
<body>
  <!-- Navigation Bar -->
  <canvas id="canvas"></canvas>
  <nav>
    <ul>
      <li><a href="reviewpage.html">Send Review</a></li>
      <li><a href="messaging.php">Messages</a></li>
      <li><a href="profile.html">Profile</a></li>
      <li><a href="../actions/logout.php">Logout</a></li>
    </ul>
  </nav>

  <!-- Header Section -->
  <header class="hero">
    <img src="../assets/boyholdingphone.png" alt="Boy Texting" class="slide-left">
    <div class="hero-content">
      <h1>Find Your Real Connections</h1>
      <p>We are committed to helping you find your match. Start your journey today!</p>
      <button class="cta-button">Join MatchMaker</button>
    </div>
    <img src="../assets/girlholdingphone.png" alt="Girl Texting" class="slide-right">
  </header>

  <!-- Profiles Section -->
  <section class="profiles">
    <h2>Explore Profiles</h2>
    <div class="profile-grid">

    <?php
      include "../db/config.php";

// Query to fetch users and their interests
$sql = "SELECT p.user_id, p.first_name, p.last_name, p.profile_image, p.hobbies
        FROM profiles p
        JOIN user_interests ui ON p.user_id = ui.user_id
        GROUP BY p.user_id";

// Execute query
$result = $conn->query($sql);

// Check if there are any users
if ($result->num_rows > 0) {
    // Output data of each user
    while ($row = $result->fetch_assoc()) {
        echo '<div class="profile-card">';
        echo '<img src="uploads/' . $row['profile_image'] . '" alt="Profile Picture">';
        echo '<h3>' . $row['first_name'] . ' ' . $row['last_name'] . '</h3>';
        echo '<p>Interests: ' . $row['hobbies'] . '</p>';
        echo '<form action="../actions/compatible.php" method="POST">';
        echo '<input type="hidden" name="selected_user_id" value="' . $row['user_id'] . '">';
        echo '<button type="submit" name="connect_button">Connect</button>';
        echo '</form>';
        echo '</div>';
    }
} else {
    echo "No users found.";
}

$conn->close();
?>

  </section>

  <!-- Footer Section -->
  <footer>
    <p>&copy; 2024 MatchMaker. All rights reserved.</p>
  </footer>

</body>
</html>
