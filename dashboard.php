<?php
include('sidebar.php');
include('database.php');
include("session.php");

if ($_SESSION['role'] != "admin") {
    // If user is not admin, block access
    header("Location: user.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard | CORE3 Customer Relationship & Business Control</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  <style>
    :root {
      --sidebar-width: 250px;
      --primary-color: #4e73df;
      --secondary-color: #f8f9fc;
      --dark-bg: #1a1a2e;
      --dark-card: #16213e;
      --text-light: #f8f9fa;
      --text-dark: #212529;
      --success-color: #1cc88a;
      --info-color: #36b9cc;
      --border-radius: 0.35rem;
      --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      overflow-x: hidden;
      background-color: var(--secondary-color);
      color: var(--text-dark);
    }

    body.dark-mode {
      --secondary-color: var(--dark-bg);
      background-color: var(--secondary-color);
      color: var(--text-light);
    }

    /* Sidebar */
    .sidebar {
      width: var(--sidebar-width);
      height: 100vh;
      position: fixed;
      left: 0;
      top: 0;
      background: #2c3e50;
      color: white;
      padding: 0;
      transition: all 0.3s ease;
      z-index: 1000;
      transform: translateX(0);
    }

    .sidebar.collapsed {
      transform: translateX(-100%);
    }

    .sidebar .logo {
      padding: 1.5rem;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar .logo img {
      max-width: 100%;
      height: auto;
    }

    .system-name {
      padding: 0.5rem 1.5rem;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      margin-bottom: 1rem;
    }

    .sidebar a {
      display: block;
      color: rgba(255, 255, 255, 0.8);
      padding: 0.75rem 1.5rem;
      text-decoration: none;
      border-left: 3px solid transparent;
      transition: all 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.1);
      color: white;
      border-left: 3px solid white;
    }

    .admin-feature {
      background-color: rgba(0, 0, 0, 0.1);
    }

    /* Main Content */
    .content {
      margin-left: var(--sidebar-width);
      padding: 20px;
      transition: all 0.3s ease;
    }

    .content.expanded {
      margin-left: 0;
    }

    /* Header */
    .header {
      background-color: white;
      padding: 1rem;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .dark-mode .header {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .hamburger {
      font-size: 1.5rem;
      cursor: pointer;
      padding: 0.5rem;
    }

    .system-title {
      color: var(--primary-color);
      font-size: 1rem;
    }

    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .card {
      background-color: white;
      border: none;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 1.5rem;
    }

    .dark-mode .card {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 0.5rem 1.75rem 0 rgba(58, 59, 69, 0.2);
    }

    .stat-value {
      font-size: 1.5rem;
      font-weight: 700;
    }

    /* Chart */
    .chartarea {
      display: flex;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .dark-mode .area {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .chart1 {
      height: 360px;
      width: 65%;
      background-color: white;
      border: none;
      text-align: center;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 1rem;
    }

    .dark-mode .chart1 {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    .chart2 {
      height: 360px;
      width: 35%;
      background-color: white;
      border: none;
      text-align: center;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
      padding: 1rem;
    }

    .dark-mode .chart2 {
      background-color: var(--dark-card);
      color: var(--text-light);
    }


    /* Table Section */
    .table-section {
      background-color: white;
      text-align: center;
      padding: 1.5rem;
      border-radius: var(--border-radius);
      box-shadow: var(--shadow);
    }

    .dark-mode .table-section {
      background-color: var(--dark-card);
      color: var(--text-light);
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .dark-mode th,
    .dark-mode td {
      border-bottom-color: #3a4b6e;
    }

    thead {
      background-color: var(--primary-color);
      color: white;
    }

    /* Theme Toggle */
    .theme-toggle-container {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .theme-switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .theme-switch input {
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
      transition: .4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }

    input:checked+.slider {
      background-color: var(--primary-color);
    }

    input:checked+.slider:before {
      transform: translateX(26px);
    }

    /* Responsive */
    @media (max-width: 992px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .content {
        margin-left: 0;
      }
    }
  </style>
</head>

<body>


  <div class="content" id="mainContent">
    <div class="header">
      <div class="hamburger" id="hamburger">☰</div>
      <div>
        <h1>Admin Dashboard <span class="system-title">| (CORE 3)</span></h1>
      </div>
      <div class="theme-toggle-container">
        <span class="theme-label">Dark Mode</span>
        <label class="theme-switch">
          <input type="checkbox" id="themeToggle">
          <span class="slider"></span>
        </label>
      </div>
    </div>

    <div class="dashboard-cards">
      <div class="card">
        <h3>Total crm</h3>
        <div class="stat-value" id="Total Users" style="text-align: center;">
          <?php
          $result = mysqli_query($conn, "SELECT COUNT(*)AS TOTAL FROM CRM2");
          $row = mysqli_fetch_assoc($result);
          echo $row['TOTAL'];
          ?>
        </div>

      </div>

      <div class="card">
        <h3>Active Contracts</h3>
        <div class="stat-value" id="Active Contracts" style ="text-align: center;">

          <?php
          // Count Active Contracts
          $activeQuery = $conn->query("SELECT COUNT(*) AS total_active FROM contracts WHERE status='Active'");
          $activeRow = $activeQuery->fetch_assoc();
          $activeContracts = $activeRow['total_active'];
          echo $activeContracts; ?></div>
        <div class="stat-label">
        </div>
      </div>

      <div class="card">
        <h3>Total user</h3>
          <div class="stat-value" id="Total Users" style="text-align: center;">
          <?php
          $result = mysqli_query($conn, "SELECT COUNT(*)AS TOTAL FROM users");
          $row = mysqli_fetch_assoc($result);
          echo $row['TOTAL'];
          ?>
        </div>
       
      </div>

      <div class="card">
        <h3>System Alert</h3>
        <div class="stat-value" id="System Alert">0</div>
        <div class="stat-label">Loading data...</div>
      </div>
    </div>


    <div class="chartarea">
      <div class="chart1">
        <h3>User Growth</h3>
        <canvas id="myChart1" style="height:200px;width:500px;"></canvas>
      </div>

      <div class="chart2">
        <h3>Contracts Overview</h3>
        <canvas id="myChart2" style="height:110px;width:150px;"></canvas>
      </div>
    </div>

    <div class="table-section">
  <h3>Recent Activity</h3>
  <table id="employeesTable">
    <thead>
      <tr>
        <th>Date</th>
        <th>Activity</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $activityQuery = $conn->query("SELECT * FROM recent_activity ORDER BY activity_date DESC LIMIT 10");
      if ($activityQuery->num_rows > 0) {
        while ($row = $activityQuery->fetch_assoc()) {
          echo "<tr>
                  <td>" . date("Y-m-d H:i", strtotime($row['activity_date'])) . "</td>
                  <td>" . htmlspecialchars($row['activity']) . "</td>
                  <td>" . htmlspecialchars($row['status']) . "</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='3'>No recent activity found</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>


  <script>
    const checkbox = document.getElementById("themeToggle");

    if (localStorage.getItem("darkMode") === "enabled") {
      document.body.classList.add("dark-mode");
      checkbox.checked = true;
    }

    checkbox.addEventListener("change", () => {
      if (checkbox.checked) {
        document.body.classList.add("dark-mode");
        localStorage.setItem("darkMode", "enabled");
      } else {
        document.body.classList.remove("dark-mode");
        localStorage.setItem("darkMode", "disabled");
      }
    });



    document.getElementById('hamburger').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('collapsed');
      document.getElementById('mainContent').classList.toggle('expanded');
    });

    const xValues = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sept", "Oct", "Nov", "Dec"];
    const yValues = [];
    new Chart("myChart1", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: 'Users',
          fill: false,
          lineTension: 0,
          backgroundColor: "rgba(0,0,255,1.0)",
          borderColor: "rgba(86, 120, 177, 0.92)",
          data: yValues
        }]
      },
      options: {
        legend: {
          display: true,
        },
        scales: {
          yAxes: [{
            ticks: {
              min: 0,
              max: 1400,
            }
          }],
        }
      }
    });

    const aValues = ["Active", "Pending", "Expired"];
    const bValues = [1, 1, 1];
    const barColors = [
      "#1e7145",
      "rgba(185, 214, 59, 1)",
      "#b91d47"
    ];

    new Chart("myChart2", {
      type: "doughnut",
      data: {
        labels: aValues,
        datasets: [{
          backgroundColor: barColors,
          data: bValues
        }]
      },
      options: {
        title: {
          display: true,
        }
      }
    });
  </script>
</body>

</html>