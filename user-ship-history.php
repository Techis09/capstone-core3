<?php
include('session.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shipment History - Freight System</title>
  <style>
    :root {
      --bg-color: #f8f9fa;
      --text-color: #000;
      --container-bg: #fff;
      --table-header-bg: #007bff;
      --table-header-text: #fff;
      --hover-color: #f1f1f1;
    }

    body.dark {
      --bg-color: #121212;
      --text-color: #f8f9fa;
      --container-bg: #1e1e1e;
      --table-header-bg: #0056b3;
      --table-header-text: #fff;
      --hover-color: #2a2a2a;
    }

    body {
      font-family: Arial, sans-serif;
      background: var(--bg-color);
      color: var(--text-color);
      margin: 0;
      padding: 0;
      display: flex;
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
      text-align: center;
      margin-bottom: 20px;
    }

     .sidebar a {
      display: block;
      padding: 12px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover {
      background: #0056b3;
    }

    /* Main content */
    .main {
      margin-left: 220px;
      padding: 20px;
      width: calc(100% - 220px);
    }

    /* Dark mode toggle */
    .toggle-container {
      text-align: right;
      margin-bottom: 10px;
    }

    .toggle-switch {
      position: relative;
      display: inline-block;
      width: 50px;
      height: 24px;
    }

    .toggle-switch input {
      display: none;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 24px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 18px;
      width: 18px;
      left: 3px;
      bottom: 3px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: #007bff;
    }

    input:checked+.slider:before {
      transform: translateX(26px);
    }

    /* Container */
    .container {
      max-width: 1000px;
      margin: 20px auto;
      background: var(--container-bg);
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 20px;
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th,
    td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      background: var(--table-header-bg);
      color: var(--table-header-text);
    }

    tr:hover {
      background: var(--hover-color);
    }

    .status {
      font-weight: bold;
      color: green;
    }

    .pending {
      color: orange;
    }

    .delivered {
      color: blue;
    }

    .cancelled {
      color: red;
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
    <a href="user-shipment-history.php">📜 Shipment History</a>
    <a href="user-profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main content -->
  <div class="main">
    <div class="toggle-container">
      <label class="toggle-switch">
        <input type="checkbox" id="darkToggle">
        <span class="slider"></span>
      </label>
    </div>

    <div class="container">
      <h2>📜 Shipment History</h2>

      <table>
        <thead>
          <tr>
            <th>Tracking No</th>
            <th>Origin</th>
            <th>Destination</th>
            <th>Weight (kg)</th>
            <th>Status</th>
            <th>Booked Date</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Example static data (replace with database query later)
          $shipments = [
            ["FRT12345", "Manila", "Cebu", "25", "In Transit", "2025-09-01"],
            ["FRT67890", "Davao", "Manila", "10", "Delivered", "2025-08-28"],
            ["FRT54321", "Cebu", "Iloilo", "15", "Pending", "2025-08-25"],
          ];

          foreach ($shipments as $s) {
            $statusClass = strtolower(str_replace(" ", "", $s[4]));
            echo "<tr>
                  <td>{$s[0]}</td>
                  <td>{$s[1]}</td>
                  <td>{$s[2]}</td>
                  <td>{$s[3]}</td>
                  <td class='{$statusClass}'>{$s[4]}</td>
                  <td>{$s[5]}</td>
                </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
    // Dark mode toggle
    const toggle = document.getElementById('darkToggle');
    toggle.addEventListener('change', () => {
      document.body.classList.toggle('dark');
    });
  </script>

</body>

</html>