<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CO Login - E-Dossier</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="fonts/google.css" rel="stylesheet">
  <style>


    body {
      font-family: 'Poppins', sans-serif;
      background: url("images/camo.png") repeat center center fixed;
      background-size: cover;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      animation: fadeInBody 1.2s ease-in-out;
    }

    @keyframes fadeInBody {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    /* Login Card with Hero-Style Glass Effect */
    .login-card {
      width: 400px;
      background: rgba(255, 255, 255, 0.12);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      padding: 35px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
      border: 2px solid rgba(255,255,255,0.25);
      text-align: center;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 1s ease-in-out;
    }

    /* Gradient Glow Border */
    .login-card::before {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(135deg, rgba(13,110,253,0.6), rgba(102,16,242,0.6));
      z-index: -1;
      filter: blur(40px);
    }

    @keyframes fadeInUp {
      from {opacity: 0; transform: translateY(30px);}
      to {opacity: 1; transform: translateY(0);}
    }

    .login-card h3 {
      font-weight: 800;
      font-size: 2rem;
      color: #fff;
      text-shadow: 2px 2px 10px rgba(0,0,0,0.6);
      margin-bottom: 20px;
    }

    .form-label {
      font-weight: 600;
      color: #f8f9fa;
      text-align: left;
      display: block;
    }

    .form-control {
      border-radius: 12px;
      padding: 12px;
      border: none;
      background: rgba(255,255,255,0.9);
      transition: all 0.3s ease;
    }
    .form-control:focus {
      border: none;
      outline: none;
      box-shadow: 0 0 10px rgba(13,110,253,0.6);
    }

    .btn-primary {
      background: #0d6efd;
      border: none;
      border-radius: 12px;
      padding: 12px;
      font-weight: 600;
      font-size: 1.1rem;
      transition: 0.3s;
      box-shadow: 0 0 12px rgba(13,110,253,0.6);
    }
    .btn-primary:hover {
      background: #0b5ed7;
      transform: translateY(-3px);
      box-shadow: 0 0 20px rgba(13,110,253,0.9);
    }

    .footer-text {
      text-align: center;
      font-size: 0.9rem;
      color: #e0e0e0;
      margin-top: 15px;
    }
    .footer-text a {
      color: #ffc107;
      font-weight: 600;
      text-decoration: none;
    }
    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3>CO Login</h3>
    <form action="co_auth.php" method="POST">
      <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="footer-text">
      <small>Part of <strong>E-Dossier</strong> System</small><br>
      <a href="co_change_password.php">Change Password</a>
    </div>
  </div>
</body>
</html>
