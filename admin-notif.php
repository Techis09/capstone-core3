<?php
include('database.php'); // db connection

// ------------------- HANDLE FEEDBACK SUBMIT -------------------
if (isset($_POST['submitFeedback'])) {
    $username  = $conn->real_escape_string($_POST['username']);
    $message   = $conn->real_escape_string($_POST['message']);
    $sentiment = $conn->real_escape_string($_POST['sentiment']);

    $sql = "INSERT INTO feedback (username, message, sentiment) 
            VALUES ('$username', '$message', '$sentiment')";
    if ($conn->query($sql)) {
        $alert = "<div class='alert alert-success'>✅ Feedback submitted successfully!</div>";
    } else {
        $alert = "<div class='alert alert-danger'>❌ Error: " . $conn->error . "</div>";
    }
}

// ------------------- FETCH FEEDBACK -------------------
$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");

$totalFeedback = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Notifications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="container py-4">

  <!-- NAVBAR WITH NOTIFICATION -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Feedback System</a>
      <div class="d-flex">
        <!-- Notification Bell -->
        <div class="dropdown">
          <button class="btn btn-secondary position-relative" data-bs-toggle="dropdown">
            🔔
            <?php if ($totalFeedback > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $totalFeedback ?>
              </span>
            <?php endif; ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" style="width:300px; max-height:400px; overflow:auto;">
            <li class="dropdown-header">Recent Feedback</li>
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <li class="dropdown-item small">
                  <strong><?= htmlspecialchars($row['username']) ?></strong> 
                  <span class="badge bg-secondary"><?= $row['sentiment'] ?></span><br>
                  <?= htmlspecialchars($row['message']) ?><br>
                  <small class="text-muted">⏰ <?= $row['created_at'] ?></small>
                </li>
                <li><hr class="dropdown-divider"></li>
              <?php endwhile; ?>
            <?php else: ?>
              <li class="dropdown-item">No feedback yet.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <!-- ALERT MESSAGE -->
  <?php if (!empty($alert)) echo $alert; ?>

  <!-- FEEDBACK FORM -->
  <div class="card mb-4">
    <div class="card-header">Submit Feedback</div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <input type="text" name="username" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="mb-3">
          <textarea name="message" class="form-control" rows="3" placeholder="Write your feedback..." required></textarea>
        </div>
        <div class="mb-3">
          <select name="sentiment" class="form-select" required>
            <option value="">Select Sentiment</option>
            <option value="Positive">😊 Positive</option>
            <option value="Negative">☹️ Negative</option>
            <option value="Suggestion">💡 Suggestion</option>
          </select>
        </div>
        <button type="submit" name="submitFeedback" class="btn btn-primary">Submit</button>
      </form>
    </div>
  </div>

</body>
</html>
