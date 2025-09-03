<?php
include('session.php');
include('database.php');

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
    $targetFile = $targetDir . $fileName;

    $allowedTypes = ['jpg','jpeg','png','gif'];
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFile)) {
            $username = $_SESSION['username'];
            $sql = "UPDATE users SET profile_image = '$targetFile' WHERE username = '$username'";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['profile_image'] = $targetFile;
                header("Location: user-profile.php?success=1");
                exit();
            }
        }
    }
}

// Fetch current user info
$username = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
$user = mysqli_fetch_assoc($query);

// Profile image fallback
$profileImage = !empty($user['profile_image']) ? $user['profile_image'] : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile - Freight System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      font-family: Arial, sans-serif;
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
    .profile-card {
      max-width: 700px;
      margin: auto;
      padding: 30px;
      border-radius: 15px;
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    .profile-card img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 4px solid #0d6efd;
      object-fit: cover;
      margin-bottom: 15px;
    }
    .profile-info {
      margin: 15px 0;
      text-align: left;
    }
    .profile-info p {
      margin: 6px 0;
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
    <a href="user-ship-history.php">📜 Shipment History</a>
    <a href="user-profile.php" class="active">👤 Profile</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="profile-card">
      <img src="<?php echo $profileImage; ?>" alt="Profile Picture">
      <h3><?php echo $user['username']; ?></h3>
      <p class="text-muted">👤 <?php echo $user['role'] ?? 'User'; ?></p>

      <div class="profile-info">
        <p><strong>Email:</strong> <?php echo $user['email'] ?? 'Not Set'; ?></p>
        <p><strong>Joined:</strong> <?php echo $user['created_at'] ?? ''; ?></p>
      </div>

      <!-- Upload Form -->
      <form method="POST" enctype="multipart/form-data" class="mt-3">
        <div class="input-group mb-3">
          <input type="file" name="profile_pic" accept="image/*" class="form-control" required>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </form>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success mt-3">✅ Profile picture updated successfully!</div>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
