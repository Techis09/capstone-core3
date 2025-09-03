<?php
include('session.php');
include('database.php'); // Make sure DB connection is available

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
  <title>Shipment History - Freight System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .sidebar a {
      display: block;
      padding: 12px;
      color: white;
      text-decoration: none;
      transition: background 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: #0056b3;
      border-radius: 5px;
    }

    /* Main content */
    .main {
      margin-left: 240px;
      padding: 20px;
      width: calc(100% - 240px);
    }

    /* Top bar */
    .topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    /* Dark mode toggle */
    .theme-toggle {
      cursor: pointer;
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

    .pending {
      color: orange;
      font-weight: bold;
    }

    .intransit {
      color: green;
      font-weight: bold;
    }

    .delivered {
      color: blue;
      font-weight: bold;
    }

    .cancelled {
      color: red;
      font-weight: bold;
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
    <a href="user-book-shipment.php">📝 Book Shipment</a>
    <a href="user-shipment-history.php" class="active">📜 Shipment History</a>
    <a href="user-profile.php">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main content -->
  <div class="main">
   <!-- Topbar -->
<div class="topbar d-flex justify-content-end align-items-center gap-3">
  <!-- Dark mode toggle -->
  <div class="form-check form-switch theme-toggle mb-0">
    <input class="form-check-input" type="checkbox" id="theme-toggle">
    <label class="form-check-label" for="theme-toggle">🌙</label>
  </div>

  <!-- Profile image dropdown -->
  <div class="dropdown">
    <img src="<?php echo $profileImage; ?>" alt="Profile"
         class="rounded-circle"
         style="width:55px; height:55px; object-fit:cover; border:2px solid #0d6efd; cursor:pointer;"
         id="profileDropdown"
         data-bs-toggle="dropdown"
         aria-expanded="false">
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
      <li><a class="dropdown-item" href="user-profile.php">👤 My Profile</a></li>
      <li><a class="dropdown-item" href="settings.php">⚙️ Settings</a></li>
      <li><a class="dropdown-item" href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>
</div>

    <!-- Shipment History -->
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
          // Example static data (replace later with DB query)
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Dark Mode Script -->
  <script>
    document.getElementById("theme-toggle").addEventListener("change", function() {
      document.body.classList.toggle("dark");
    });
  </script>
</body>
</html>
