<?php
    $servername = "localhost";
    $username = "root";
    $password =  "";
    $dbname = "rush_db";
    // Database connection (adjust with your credentials)
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check for connection error
    if ($conn->connect_error) {
        die("Database connection failed: ".$conn->connect_error);
    }
    else {
       // echo "Connected Successfully";
    }
?>