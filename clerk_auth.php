<?php
session_start();
include 'connect.php'; // DB connection

$u = $_POST['username'];
$p = $_POST['password'];
$p_md5 = md5($p);

$sql = "SELECT * FROM users WHERE username=? AND password=? AND role='Clerk' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $u, $p_md5);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    header("Location: cleark_dashboard.php");
    exit();
} else {
    echo "<script>alert('❌ Invalid Clerk credentials'); window.location='clerk_login.php';</script>";
}
?>
