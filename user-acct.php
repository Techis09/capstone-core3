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
  <title>User Dashboard - Freight System</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f8f9fa;
    }

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
      background-color: #0b5ed7;
      border-radius: 5px;
    }

    .main-content {
      margin-left: 240px;
      padding: 20px;
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .table thead {
      background: #0d6efd;
      color: white;
    }

    .dark-mode {
      background-color: #1a1a2e;
      color: white;
    }

    .dark-mode .card {
      background-color: #16213e;
      color: white;
    }

    .dark-mode .table {
      color: white;
    }

    .theme-toggle {
      cursor: pointer;
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
    <a href="user-acct.php" class="active">🏠 Dashboard</a>
    <a href="user-shipment.php">📦 Track Shipment</a>
    <a href="user-book-shipment.php">📝 Book Shipment</a>
    <a href="user-ship-history.php">📜 Shipment History</a>
    
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Welcome, <?php echo $_SESSION['username']; ?> 👋</h3>

      <div class="d-flex align-items-center gap-3">
        <!-- Dark Mode Toggle -->
        <div class="form-check form-switch theme-toggle mb-0">
          <input class="form-check-input" type="checkbox" id="theme-toggle">
          <label class="form-check-label" for="theme-toggle">🌙</label>
        </div>

        <!-- Profile Picture Dropdown -->
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
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card p-3 text-center">
          <h5>Total Shipments</h5>
          <h2>12</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center">
          <h5>In Transit</h5>
          <h2>3</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center">
          <h5>Delivered</h5>
          <h2>8</h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card p-3 text-center">
          <h5>Pending</h5>
          <h2>1</h2>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="card p-3">
          <h5>📊 Shipment Trends</h5>
          <canvas id="shipmentTrends"></canvas>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card p-3">
          <h5>🚛 Status Breakdown</h5>
          <canvas id="statusBreakdown"></canvas>
        </div>
      </div>
    </div>

    <!-- Recent Shipments Table -->
    <div class="card p-3">
      <h5>Recent Shipments</h5>
      <div class="table-responsive">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Tracking No</th>
              <th>Origin</th>
              <th>Destination</th>
              <th>Status</th>
              <th>Booked Date</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>FRT12345</td>
              <td>Manila</td>
              <td>Cebu</td>
              <td><span class="badge bg-warning">In Transit</span></td>
              <td>2025-09-01</td>
            </tr>
            <tr>
              <td>FRT67890</td>
              <td>Davao</td>
              <td>Manila</td>
              <td><span class="badge bg-success">Delivered</span></td>
              <td>2025-08-28</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS + Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Dark Mode
    document.getElementById("theme-toggle").addEventListener("change", function() {
      document.body.classList.toggle("dark-mode");
    });

    // Charts Example
    const ctx1 = document.getElementById('shipmentTrends').getContext('2d');
    new Chart(ctx1, {
      type: 'line',
      data: {
        labels: ['Aug 25', 'Aug 26', 'Aug 27', 'Aug 28', 'Aug 29'],
        datasets: [{
          label: 'Shipments',
          data: [2, 5, 3, 6, 4],
          borderColor: '#0d6efd',
          fill: false
        }]
      }
    });

    const ctx2 = document.getElementById('statusBreakdown').getContext('2d');
    new Chart(ctx2, {
      type: 'pie',
      data: {
        labels: ['In Transit', 'Delivered', 'Pending'],
        datasets: [{
          data: [3, 8, 1],
          backgroundColor: ['#ffc107', '#198754', '#0dcaf0']
        }]
      }
    });
  </script>
</body>

</html>