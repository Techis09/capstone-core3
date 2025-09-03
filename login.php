<?php
session_start();
include("database.php");

$alert = ""; // store alert script


// Initialize session status properly
if (!isset($_SESSION['status']) || $_SESSION['status'] === "invalid") {
  $_SESSION['status'] = "invalid";
 

}

if (isset($_SESSION['status']) && $_SESSION['status'] === "login") {
 $_SESSION['status'] = "login";
  header("Location: dashboard.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);

  // admin acct
  $admin_username = 'admins';
  $addmin_password = 'admin123'; 


  if ($username === $admin_username && $password === $addmin_password) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'admin';
    $_SESSION['status'] = "login";

    $alert = "<script>
            Swal.fire({
              icon: 'success',
              title: '<span style=\"color:#fff\">Login Successful!</span>',
              html: '<span style=\"color:#ecf0f1\">Welcome, admin!</span>',
              background: '#2c3e50',
              confirmButtonColor: '#0072ff'
            }).then(()=>{ 
              window.location.href='dashboard.php'; 
            });
          </script>";
  } else {



    // Fetch user from DB
    $sql = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = $conn->query($sql);


    if ($result->num_rows > 0) {
      $user = $result->fetch_assoc();


      // Verify hashed password
      if (password_verify($password, $user['password'])) {

        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'user';
        $_SESSION['status'] = "login";

        $alert = "<script>
            Swal.fire({
              icon: 'success',
              title: '<span style=\"color:#fff\">Login Successful!</span>',
              html: '<span style=\"color:#ecf0f1\">Welcome, " . addslashes($user['profile_name']) . "!</span>',
              background: '#2c3e50',
              confirmButtonColor: '#0072ff'
            }).then(()=>{ 
              window.location.href='user-acct.php'; 
            });
          </script>";
      } else {  

        $_SESSION['status'] = "invalid";

        $alert = "<script>
            Swal.fire({
              icon: 'error',
              title: '<span style=\"color:#fff\">Invalid Password</span>',
              html: '<span style=\"color:#ecf0f1\">Please try again.</span>',
              background: '#2c3e50',
              confirmButtonColor: '#d33'
            });
          </script>";
      }
    } else {
      $_SESSION['status'] = "invalid";


      $alert = "<script>
            Swal.fire({
              icon: 'error',
              title: '<span style=\"color:#fff\">User Not Found</span>',
              html: '<span style=\"color:#ecf0f1\">No account exists with that username.</span>',
              background: '#2c3e50',
              confirmButtonColor: '#d33'  
            });
          </script>";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - SLATE System</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
      color: white;
      line-height: 1.6;
    }

    .main-container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-container {
      width: 100%;
      max-width: 75rem;
      display: flex;
      background: rgba(31, 42, 56, 0.8);
      border-radius: 0.75rem;
      overflow: hidden;
      box-shadow: 0 0.625rem 1.875rem rgba(0, 0, 0, 0.3);
    }

    .welcome-panel {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2.5rem;
      background: linear-gradient(135deg, rgba(0, 114, 255, 0.2), rgba(0, 198, 255, 0.2));
    }

    .welcome-panel h1 {
      font-size: 2.25rem;
      font-weight: 700;
      text-align: center;
      color: #fff;
    }

    .login-panel {
      width: 25rem;
      padding: 3.75rem 2.5rem;
      background: rgba(22, 33, 49, 0.95);
    }

    .login-box {
      text-align: center;
    }

    .login-box img {
      width: 6.25rem;
      margin-bottom: 1.25rem;
    }

    .login-box h2 {
      margin-bottom: 1.5625rem;
      font-size: 1.75rem;
      color: #fff;
    }

    .login-box form {
      display: flex;
      flex-direction: column;
      gap: 1.25rem;
    }

    .login-box input {
      padding: 0.75rem;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 0.375rem;
      color: white;
      font-size: 1rem;
    }

    .login-box button {
      padding: 0.75rem;
      background: linear-gradient(to right, #0072ff, #00c6ff);
      border: none;
      border-radius: 0.375rem;
      font-weight: 600;
      font-size: 1rem;
      color: white;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .login-box button:hover {
      background: linear-gradient(to right, #0052cc, #009ee3);
      transform: translateY(-0.125rem);
      box-shadow: 0 0.3125rem 0.9375rem rgba(0, 0, 0, 0.2);
    }

    footer {
      text-align: center;
      padding: 1.25rem;
      background: rgba(0, 0, 0, 0.2);
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.875rem;
    }
  </style>
</head>

<body>
  <div class="main-container">
    <div class="login-container">
      <div class="welcome-panel">
        <h1>FREIGHT MANAGEMENT SYSTEM</h1>

      </div>
      <div class="login-panel">
        <div class="login-box">
          <img src="logo.png" alt="SLATE Logo">
          <h2>SLATE Login</h2>
          <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
            <div style="margin-top: 1rem;">
              <a href="register.php" style="color:#00c6ff;text-decoration:underline;">Create an account</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer>
    &copy; <span id="currentYear"></span> SLATE Freight Management System. All rights reserved.
  </footer>

  <script>
    document.getElementById('currentYear').textContent = new Date().getFullYear();
  </script>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Run PHP alert -->
  <?php if (!empty($alert))
    echo $alert; ?>
</body>

</html>