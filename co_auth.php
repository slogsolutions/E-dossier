<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST['username'];
    $p = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'CO'");
    $stmt->bind_param("s", $u);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($row['password'] === md5($p)) {
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];
            header("Location: dashboard.php");  // ✅ CO dashboard
            exit();
        } else {
            echo "<script>alert('❌ Wrong password'); window.location='co_login.php';</script>";
        }
    } else {
        echo "<script>alert('❌ User not found or not CO'); window.location='co_login.php';</script>";
    }
}
?>
