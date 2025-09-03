<?php
include('session.php');
include('database.php'); // DB connection

// Fetch profile image
$username = $_SESSION['username'];
$sql = "SELECT profile_image FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

// Fallback image
$profileImage = !empty($row['profile_image']) ? $row['profile_image'] : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Shipment - Freight System</title>
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

    /* 🌙 Dark Mode */
    .dark-mode {
      background-color: #212529 !important;
      color: white !important;
    }

    .dark-mode .card {
      background-color: #343a40 !important;
      color: white !important;
    }

    .dark-mode .form-control,
    .dark-mode textarea,
    .dark-mode select {
      background-color: #495057 !important;
      color: white !important;
      border: 1px solid #6c757d !important;
    }

    .dark-mode .form-control::placeholder,
    .dark-mode textarea::placeholder {
      color: #ced4da !important;
    }

    .dark-mode .form-label {
      color: #f8f9fa !important;
    }

    .dark-mode .btn-primary {
      background-color: #0d6efd !important;
      border-color: #0d6efd !important;
    }

    /* Profile header */
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
    <a href="user-shipment.php">📦 Track Shipment</a>
    <a href="user-book-shipment.php" class="active">📝 Book Shipment</a>
    <a href="user-ship-history.php">📜 Shipment History</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Profile Header -->
    <div class="profile-header">
      <!-- Dark Mode Toggle -->
      <div class="form-check form-switch theme-toggle mb-0 me-3">
        <input class="form-check-input" type="checkbox" id="darkModeSwitch">
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

    <h2 class="mb-4">📝 Book a Shipment</h2>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $sender = $_POST['sender'];
      $receiver = $_POST['receiver'];
      $origin = $_POST['origin'];
      $destination = $_POST['destination'];
      $weight = $_POST['weight'];
      $description = $_POST['description'];

      echo "<div class='alert alert-success'>✅ Shipment booked successfully!<br>
            <strong>Tracking No:</strong> FRT" . rand(10000, 99999) . "</div>";
    }
    ?>

    <div class="card p-4">
      <form method="POST">
        <div class="mb-3">
          <label for="sender" class="form-label">Sender Name</label>
          <input type="text" name="sender" id="sender" class="form-control" placeholder="Enter sender name" required>
        </div>

        <div class="mb-3">
          <label for="receiver" class="form-label">Receiver Name</label>
          <input type="text" name="receiver" id="receiver" class="form-control" placeholder="Enter receiver name" required>
        </div>

        <div class="mb-3">
          <label for="origin" class="form-label">Origin</label>
          <input type="text" name="origin" id="origin" class="form-control" placeholder="Enter origin" required>
        </div>

        <div class="mb-3">
          <label for="destination" class="form-label">Destination</label>
          <input type="text" name="destination" id="destination" class="form-control" placeholder="Enter destination" required>
        </div>

        <div class="mb-3">
          <label for="weight" class="form-label">Weight (kg)</label>
          <input type="number" step="0.1" name="weight" id="weight" class="form-control" placeholder="Enter weight" required>
        </div>

        <div class="mb-3">
          <label for="description" class="form-label">Package Description</label>
          <textarea name="description" id="description" rows="3" class="form-control" placeholder="Enter package details" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Book Shipment</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById("darkModeSwitch").addEventListener("change", () => {
      document.body.classList.toggle("dark-mode");
    });
  </script>

</body>
</html>
