<?php
include("database.php");

// Disable MySQLi exceptions so we can handle errors manually
mysqli_report(MYSQLI_REPORT_OFF);

$alert = ""; // store alert script

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profile_name = $conn->real_escape_string($_POST['profile_name']);
    $username     = $conn->real_escape_string($_POST['username']);
    $password     = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (profile_name, username, password) 
            VALUES ('$profile_name', '$username', '$password')";

    if ($conn->query($sql) === TRUE) {
        $alert = "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registration successful!',
                        text: 'You can now log in.',
                        confirmButtonColor: '#0072ff'
                    }).then(()=>{ window.location.href='login.php'; });
                  </script>";
    } else {
        if ($conn->errno == 1062) {
            $alert = "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Username already exists',
                            text: 'Please choose another one.',
                            confirmButtonColor: '#d33'
                        });
                      </script>";
        } else {
            $alert = "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Database Error',
                            text: '". addslashes($conn->error) ."',
                            confirmButtonColor: '#d33'
                        });
                      </script>";
        }
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - SLATE System</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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
        width: 100%;
        text-align: center;
    }
    .login-box img {
        width: 6.25rem;
        margin-bottom: 1.25rem;
    }
    .login-box h2 {
        margin-bottom: 1.5625rem;
        color: #ffffff;
        font-size: 1.75rem;
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
                  <h2>SLATE Registration</h2>
                  <form action="register.php" method="POST">
                      <input type="text" name="profile_name" placeholder="Profile Name" required>
                      <input type="text" name="username" placeholder="Username" required>
                      <input type="password" name="password" placeholder="Password" required>
                      <button type="submit">Register</button>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <footer>
      &copy; <span id="currentYear"></span> SLATE Freight Management System. All rights reserved.
  </footer>

  <script>
    // Add current year to footer
    document.getElementById('currentYear').textContent = new Date().getFullYear();
  </script>

  <!-- Bootstrap 5 JS with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Run PHP alert -->
  <?php if (!empty($alert)) echo $alert; ?>
</body>
</html>
