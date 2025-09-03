<?php
include('session.php');
include('database.php'); // include DB connection

// Fetch the latest profile image from DB
$username = $_SESSION['username'];
$sql = "SELECT profile_image FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Use database value, fallback to default if empty
$profileImage = !empty($row['profile_image']) ? $row['profile_image'] : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Track Shipment - Freight System</title>
  <style>
    :root {
      --primary-color: #007bff;
      --light-bg: #f8f9fa;
      --light-card: #fff;
      --dark-bg: #121212;
      --dark-card: #1e1e1e;
    }

    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: var(--light-bg);
      color: #333;
      transition: background 0.3s, color 0.3s;
    }

    /* Sidebar */
    .sidebar {
      height: 100vh;
      background: #2c3e50;
      padding-top: 20px;
      position: fixed;
      width: 240px;
      color: white;
    }

    .sidebar a {
      display: block;
      padding: 12px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 5px;
    }

    /* Main content */
    .main {
      margin-left: 240px;
      padding: 20px;
      position: relative;
    }

    .container {
      max-width: 700px;
      margin: 30px auto;
      background: var(--light-card);
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      transition: background 0.3s, color 0.3s;
    }

    h2 {
      text-align: center;
      color: var(--primary-color);
      margin-bottom: 20px;
    }

    /* Dark Mode */
    .dark-mode {
      background: var(--dark-bg);
      color: #f1f1f1;
    }

    .dark-mode .container {
      background: var(--dark-card);
      color: #f1f1f1;
      box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
    }

    .dark-mode input[type="text"] {
      background: #333;
      color: #fff;
      border: 1px solid #555;
    }

    .dark-mode .result {
      background: #2a2a2a;
      border: 1px solid #555;
    }

    /* Profile dropdown */
    .profile-header {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>

  <!-- Sidebar -->
  <div class="sidebar d-flex flex-column">
    <div class="text-center mb-4">
      <img src="logo.png" alt="Freight Logo" class="img-fluid mb-2" style="max-width:120px;">
      <h5>Freight System</h5>
    </div>
    <a href="user-acct.php">🏠 Dashboard</a>
    <a href="user-shipment.php" class="active">📦 Track Shipment</a>
    <a href="user-book-shipment.php">📝 Book Shipment</a>
    <a href="user-ship-history.php">📜 Shipment History</a>
  </div>

  <!-- Main Content -->
  <div class="main">

    <!-- Profile Header with Dark Mode -->
    <div class="profile-header">
      <!-- Dark Mode Toggle -->
      <div class="form-check form-switch theme-toggle mb-0 me-3">
        <input class="form-check-input" type="checkbox" id="darkModeSwitch" onclick="toggleDarkMode()">
        <label class="form-check-label" for="darkModeSwitch">🌙</label>
      </div>

      <!-- Profile Dropdown -->
      <div class="dropdown">
        <img src="<?php echo $profileImage; ?>" alt="Profile"
          class="rounded-circle"
          style="width:55px; height:55px; object-fit:cover; border:2px solid #0d6efd; cursor:pointer;"
          id="profileDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false">
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
          <li><a class="dropdown-item" href="user-profile.php">👤 Profile</a></li>
          <li><a class="dropdown-item" href="logout.php">🚪 Logout</a></li>
        </ul>
      </div>
    </div>

    <div class="container">
      <h2>🔍 Track Your Shipment</h2>

      <!-- Track form -->
      <form method="POST" class="d-flex gap-2 mb-3">
        <input type="text" name="tracking_number" class="form-control" placeholder="Enter Tracking Number" required>
        <button type="submit" class="btn btn-primary">Track</button>
      </form>

      <!-- Result -->
      <div class="result">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $tracking_number = trim($_POST['tracking_number']);

          // Example: Fetch from DB later
          if ($tracking_number == "FRT12345") {
            echo "<h3>Shipment Details</h3>";
            echo "<p><strong>Tracking No:</strong> FRT12345</p>";
            echo "<p><strong>Origin:</strong> Manila</p>";
            echo "<p><strong>Destination:</strong> Cebu</p>";
            echo "<p><strong>Status:</strong> <span class='status'>In Transit</span></p>";
          } else {
            echo "<p class='not-found'>❌ Shipment not found. Please check your tracking number.</p>";
          }
        }
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleDarkMode() {
      document.body.classList.toggle("dark-mode");
    }
  </script>

</body>

</html>
