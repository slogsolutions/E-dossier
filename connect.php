<?php
$servername = "localhost";   // XAMPP वर default
$username   = "root";        // XAMPP default
$password   = "";            // XAMPP default (रिकामं password)
$dbname     = "dossier_db";    // आपलं database नाव

// Connection तयार करा
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
