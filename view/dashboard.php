<?php
session_start();
include "../db/config.php";

// Query to get actual gender counts from your database
$query = "SELECT gender, COUNT(*) as count FROM profiles GROUP BY gender";
$result = $conn->query($query);

$data = array(
    'Male' => 0,
    'Female' => 0
);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[$row['gender']] = $row['count'];
    }
}

// For debugging - remove in production
echo "<!-- Data fetched from DB: ";
print_r($data);
echo " -->";
?>

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
            <button class="sidebar-btn active" onclick="location.href='dashboard.php'">Dashboard</button>
            <button class="sidebar-btn" onclick="location.href='usermanagement.php'">Users</button>
           
            <!--<button class="sidebar-btn">Profile</button>-->
            
            <button class="sidebar-btn" onclick="location.href='../actions/login.php'">Logout</button>
        </div>
    </nav>
    <main class="main-content">
        <div class="info-box">
            <h2>Total Users</h2>
            <?php 
                include "../db/config.php";

                $sql = $conn->prepare("SELECT count(user_id) from profiles ");
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
            
            <div class="chart-container">
                <h3>Rush Gender Distribution</h3>
                <canvas id="genderChart" width="400" height="200"></canvas>
                <?php
                    include "../db/config.php"; 

                    // Initialize variables to store the count of males and females
                    $male_count = 0;
                    $female_count = 0;

                    try {
                        // Query to count the number of male users
                        $sql_male = "SELECT COUNT(*) AS male_count FROM profiles WHERE gender = 'Male'";
                        $stmt_male = $conn->prepare($sql_male);
                        $stmt_male->execute();
                        $result_male = $stmt_male->get_result();
                        $male_data = $result_male->fetch_assoc();
                        $male_count = $male_data['male_count'];

                        // Query to count the number of female users
                        $sql_female = "SELECT COUNT(*) AS female_count FROM profiles WHERE gender = 'Female'";
                        $stmt_female = $conn->prepare($sql_female);
                        $stmt_female->execute();
                        $result_female = $stmt_female->get_result();
                        $female_data = $result_female->fetch_assoc();
                        $female_count = $female_data['female_count'];

                    } catch (Exception $e) {
                        // Handle any errors
                        echo "<p>Error fetching gender counts: " . htmlspecialchars($e->getMessage()) . "</p>";
                    }

                    $conn->close();
                    ?>
            </div>
        </div>
        <div class="info-box">
            <h2>Reviews</h2>
            <?php 
                include "../db/config.php";

                // Query to fetch reviews along with the user's name
                $sql = $conn->prepare("
                    SELECT reviews.review_content, reviews.rating, reviews.created_at, 
                        CONCAT(profiles.first_name, ' ', profiles.last_name) AS reviewer_name 
                    FROM reviews
                    INNER JOIN profiles ON reviews.user_id = profiles.user_id
                ");

                if ($sql->execute()) {
                    $result = $sql->get_result();

                    // Check if there are any reviews
                    if ($result->num_rows > 0) {
                        // Loop through all reviews and display them
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="review">';
                            echo '<p><strong>Reviewer:</strong> ' . htmlspecialchars($row['reviewer_name']) . '</p>';
                            echo '<p><strong>Review Content:</strong> ' . htmlspecialchars($row['review_content']) . '</p>';
                            echo '<p><strong>Rating:</strong> ' . htmlspecialchars($row['rating']) . ' / 5</p>';
                            echo '<p><strong>Posted At:</strong> ' . htmlspecialchars($row['created_at']) . '</p>';
                            echo '</div><hr>';
                        }
                    } else {
                        echo '<p>No reviews available.</p>';
                    }
                } else {
                    echo '<p>Error fetching reviews.</p>';
                }

                $sql->close();
                $conn->close();
            ?>
        </div>

    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const ctx = document.getElementById('genderChart').getContext('2d');

            const data = {
                male: <?php echo $male_count; ?>,
                female: <?php echo $female_count; ?>
            };

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['User Gender Distribution'],
                    datasets: [
                        {
                            label: 'Male',
                            data: [data.male],
                            backgroundColor: '#4299E1',
                            borderColor: '#2B6CB0',
                            borderWidth: 1,
                            borderRadius: 8,
                            barPercentage: 0.5
                        },
                        {
                            label: 'Female',
                            data: [data.female],
                            backgroundColor: '#F687B3',
                            borderColor: '#D53F8C',
                            borderWidth: 1,
                            borderRadius: 8,
                            barPercentage: 0.5
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                padding: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} users`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of Users',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                padding: 20
                            },
                            grid: {
                                color: '#E2E8F0'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    }
                }
            });
        });
    </script>
</body>
</html>
