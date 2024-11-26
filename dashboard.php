<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Rush!</title>
    <link rel="icon" href="../assets/favicon_rush.ico" type="ico">
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>
<body>
    <nav class="sidebar">
        <div class="logo">
            <h1>Rush!</h1>
        </div>
        <div class="menu">
            <button class="sidebar-btn active">Dashboard</button>
            <button class="sidebar-btn" onclick="location.href='usermanagement.php'">Users</button>
            <button class="sidebar-btn">Messages</button>
            <button class="sidebar-btn">Profile</button>
            <button class="sidebar-btn">Settings</button>
            <button class="sidebar-btn">Logout</button>
        </div>
    </nav>
    <main class="main-content">
        <div class="info-box">
            <h2>Total Users</h2>
            <?php 
                include "../db/config.php";

                $sql = $conn->prepare("SELECT count(user_id) from users ");
                if ($sql->execute()){
                    $result = $sql->get_result();
                    $row =  $result->fetch_assoc();

                    echo '<p>'.$row['count(user_id)'].'</p>';
                }
                        
                $sql->close();
            ?>
        </div>
        <div class="info-box">
            <h2>Active Matches</h2>
            <?php 
                include "../db/config.php";

                $sql = $conn->prepare("SELECT count(match_id) from matches ");
                if ($sql->execute()){
                    $result = $sql->get_result();
                    $row =  $result->fetch_assoc();

                echo '<p>'.$row['count(match_id)'].'</p>';
                }
                        
                $sql->close();
            ?>
        </div>
        <div class="chart">
            <h3>User Activity</h3>
            <p>Graph Placeholder</p>
        </div>
    </main>
</body>
</html>

