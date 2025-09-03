<?php
include('session.php');
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

    .sidebar h2 {
      margin: 0 0 30px;
      font-size: 20px;
      text-align: center;
    }

    .sidebar a {
      display: block;
      padding: 12px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }
    .sidebar a:hover {
      background: rgba(255, 255, 255, 0.2);
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

    form {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }

    input[type="text"] {
      flex: 1;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      transition: background 0.3s, color 0.3s, border 0.3s;
    }

    button {
      padding: 10px 20px;
      background: var(--primary-color);
      border: none;
      color: #fff;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }

    button:hover {
      background: #0056b3;
    }

    .result {
      padding: 15px;
      border-radius: 8px;
      border: 1px solid #ddd;
      background: #fdfdfd;
      transition: background 0.3s, color 0.3s;
    }

    .result h3 {
      margin: 0 0 10px;
      color: #333;
    }

    .status {
      font-weight: bold;
      color: green;
    }

    .not-found {
      color: red;
      font-weight: bold;
    }

    /* 🌙 Dark Mode */
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

    /* Toggle Switch */
    .dark-toggle {
      position: absolute;
      top: 20px;
      right: 30px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 25px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 25px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 4px;
      bottom: 3.5px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: #007bff;
    }

    input:checked+.slider:before {
      transform: translateX(24px);
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
    <a href="user-shipment.php">📦 Track Shipment</a>
    <a href="user-book-shipment.php" class="active">📝 Book Shipment</a>
    <a href="user-ship-history.php">📜 Shipment History</a>
    <a href="user-profile.php">👤 Profile</a>
    <a href="user-logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main">

    <!-- 🌙 Dark Mode Toggle Switch -->
    <div class="dark-toggle">
      <label class="switch">
        <input type="checkbox" id="darkModeSwitch" onclick="toggleDarkMode()">
        <span class="slider"></span>
      </label>
      <span>🌙</span>
    </div>

    <div class="container">
      <h2>🔍 Track Your Shipment</h2>

      <!-- Track form -->
      <form method="POST">
        <input type="text" name="tracking_number" placeholder="Enter Tracking Number" required>
        <button type="submit">Track</button>
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

  <script>
    function toggleDarkMode() {
      document.body.classList.toggle("dark-mode");
    }
  </script>

</body>

</html>