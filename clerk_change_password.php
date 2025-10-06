<?php
session_start();
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $old_pass = md5($_POST['old_password']);
    $new_pass = md5($_POST['new_password']);
    $confirm_pass = md5($_POST['confirm_password']);

    if ($new_pass !== $confirm_pass) {
        echo "<script>alert('❌ New password and Confirm password do not match'); window.location='clerk_change_password.php';</script>";
        exit();
    }

    $sql = "SELECT * FROM users WHERE username=? AND password=? AND role='Clerk'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $old_pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $update = "UPDATE users SET password=? WHERE username=? AND role='Clerk'";
        $stmt2 = $conn->prepare($update);
        $stmt2->bind_param("ss", $new_pass, $username);
        if ($stmt2->execute()) {
            echo "<script>alert('✅ Password changed successfully'); window.location='clerk_login.php';</script>";
        } else {
            echo "<script>alert('❌ Error updating password'); window.location='clerk_change_password.php';</script>";
        }
    } else {
        echo "<script>alert('❌ Invalid username or old password'); window.location='clerk_change_password.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password - Clerk</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                  url('images/army-bg.jpg') no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      width: 450px;
      background: #ffffffcc;
      backdrop-filter: blur(8px);
      border-radius: 15px;
      box-shadow: 0px 8px 20px rgba(0,0,0,0.4);
      padding: 30px;
      animation: fadeIn 1s ease-in-out;
    }
    .login-card h3 {
      font-weight: 700;
      text-align: center;
      color: #1a3c34;
      margin-bottom: 20px;
    }
    .form-label {
      font-weight: 600;
      color: #333;
    }
    .form-control {
      border-radius: 10px;
      padding: 12px;
      border: 1px solid #ccc;
    }
    .btn-primary {
      background: #1a3c34;
      border: none;
      border-radius: 10px;
      padding: 12px;
      font-weight: 600;
    }
    .btn-primary:hover {
      background: #2d5249;
    }
    .footer-text {
      text-align: center;
      font-size: 0.9rem;
      color: #555;
      margin-top: 15px;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3>Change Password (Clerk)</h3>
    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Old Password</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Change Password</button>
    </form>
    <div class="footer-text">
      <a href="clerk_login.php">⬅ Back to Login</a>
    </div>
  </div>
</body>
</html>
