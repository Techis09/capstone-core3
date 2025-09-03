<?php
include("database.php"); // your DB connection

// Handle feedback form submission
if (isset($_POST['submitFeedback'])) {
    $username  = $_POST['username']; // in real system: $_SESSION['username']
    $message   = $_POST['message'];
    $sentiment = $_POST['sentiment'];

    // Insert feedback
    $sql = "INSERT INTO feedback (username, message, sentiment) 
            VALUES ('$username', '$message', '$sentiment')";
    if ($conn->query($sql) === TRUE) {
        // Create notification for admin
        $notifMessage = "New feedback from $username";
        $conn->query("INSERT INTO notifications (message) VALUES ('$notifMessage')");
    }
}

// Mark notification as read
if (isset($_GET['mark_read'])) {
    $notifId = intval($_GET['mark_read']);
    $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notifId");
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?')); // refresh without query string
    exit;
}

// Fetch notifications (unread first)
$notifResult = $conn->query("SELECT * FROM notifications ORDER BY is_read ASC, created_at DESC");

// Fetch all feedback
$feedbackResult = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback System</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    form { margin-bottom: 20px; }
    textarea { width: 100%; height: 80px; }
    .notif { background: #f2f2f2; padding: 10px; margin: 5px 0; border-radius: 5px; }
    .unread { font-weight: bold; color: red; }
    .feedback { border: 1px solid #ccc; padding: 10px; margin: 10px 0; border-radius: 5px; }
    a.mark { float: right; font-size: 0.9em; }
  </style>
</head>
<body>
  <h2>User Feedback Form</h2>
  <form method="POST">
    <input type="text" name="username" placeholder="Your Name" required><br><br>
    <textarea name="message" placeholder="Enter your feedback" required></textarea><br><br>
    <select name="sentiment" required>
      <option value="Positive">Positive</option>
      <option value="Negative">Negative</option>
      <option value="Suggestion">Suggestion</option>
    </select><br><br>
    <button type="submit" name="submitFeedback">Send Feedback</button>
  </form>

  <h2>Notifications (Admin View)</h2>
  <?php while ($row = $notifResult->fetch_assoc()) { ?>
    <div class="notif <?php echo $row['is_read'] == 0 ? 'unread' : ''; ?>">
      <?php echo $row['message']; ?> - 
      <small><?php echo $row['created_at']; ?></small>
      <?php if ($row['is_read'] == 0) { ?>
        <a class="mark" href="?mark_read=<?php echo $row['id']; ?>">Mark as Read</a>
      <?php } ?>
    </div>
  <?php } ?>

  <h2>All Feedback</h2>
  <?php while ($row = $feedbackResult->fetch_assoc()) { ?>
    <div class="feedback">
      <strong><?php echo $row['username']; ?></strong> (<?php echo $row['sentiment']; ?>)<br>
      <?php echo $row['message']; ?><br>
      <small><?php echo $row['created_at']; ?></small>
    </div>
  <?php } ?>
</body>
</html>
