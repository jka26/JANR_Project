<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Matchmaker - Find Your Match</title>
  <link rel="icon" href="favicon_rush.ico" type="ico">
  <link rel="stylesheet" href="matchmaking_style.css">
</head>
<body>
  <!-- Navigation Bar -->
  <nav>
    <ul>
      <li><a href="#">Messages</a></li>
      <li><a href="#">Profile</a></li>
      <li><a href="#">Logout</a></li>
    </ul>
  </nav>

  <!-- Header Section -->
  <header class="hero">
    <img src="boyholdingphone.png" alt="Boy Texting" class="slide-left">
    <div class="hero-content">
      <h1>Find Your Real Connections</h1>
      <p>We are committed to helping you find your match. Start your journey today!</p>
      <button class="cta-button">Join MatchMaker</button>
    </div>
    <img src="girlholdingphone.png" alt="Girl Texting" class="slide-right">
  </header>

  <!-- Profiles Section -->
  <section class="profiles">
    <h2>Explore Profiles</h2>
    <div class="profile-grid">
    <?php
// Database connection
$host = 'localhost';
$dbname = 'rush_db';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch users and their interests
$sql = "SELECT u.first_name, u.last_name, u.profile_image, GROUP_CONCAT(i.interest_desc) AS interests
        FROM users u
        JOIN user_interests ui ON u.user_id = ui.user_id
        JOIN interests i ON ui.interest_id = i.interest_id
        GROUP BY u.user_id";

// Execute query
$result = $conn->query($sql);

// Check if there are any users
if ($result->num_rows > 0) {
    // Output data of each user
    while ($row = $result->fetch_assoc()) {
        echo '<div class="profile-card">';
        echo '<img src="uploads/' . $row['profile_image'] . '" alt="Profile Picture">';
        echo '<h3>' . $row['first_name'] . ' ' . $row['last_name'] . '</h3>';
        echo '<p>Interests: ' . $row['interests'] . '</p>';
        echo '<form action="calculate_compatibility.php" method="POST">';
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
