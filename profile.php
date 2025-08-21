<?php
  require_once("api/database.php");
  session_start();

  // Security check
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }

  $user_id = $_SESSION['user_id'];

  // Fetch all feedback submitted by the current user
  $sql = "SELECT * FROM feedback WHERE user_id = ? ORDER BY date DESC";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
        <div class="header-left">
            <h1>My Feedback History</h1>
            <p>A record of all your submissions.</p>
        </div>
        <div class="header-right">
            <div class="search-bar">
                <input type="text" placeholder="Search...">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </div>
            <button id="feedbackBtn" class="new-feedback-btn">+ New Feedback</button>
        </div>
    </header>

    <div class="feedback-box">
      <?php if($result && mysqli_num_rows($result) > 0): ?>
          <?php while($row = mysqli_fetch_assoc($result)): ?>
              <div class="feedback-item">
                  <div class="feedback-meta">
                      <span class="feedback-category"><?php echo htmlspecialchars($row["category"]); ?></span>
                      <span class="feedback-status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                  </div>
                  <div class="feedback-content"><?php echo htmlspecialchars($row["feedback"]); ?></div>
                  <div class="feedback-date">Submitted on: <?php echo date("d M Y, h:i A", strtotime($row["date"])); ?></div>
              </div>
          <?php endwhile; ?>
      <?php else: ?>
          <p class="no-feedback">You have not submitted any feedback yet.</p>
      <?php endif; ?>
    </div>

    <!-- Feedback Modal (Required for the button to work) -->
    <div id="feedbackModal" class="modal">
      <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Submit Your Feedback</h2>
        <form id="feedbackForm" action="student_dashboard.php" method="post">
           <div class="form-group">
               <label for="category">Category:</label>
               <select name="category" id="category" required>
                   <option value="" disabled selected>Select a category</option>
                   <option value="Academics">Academics</option>
                   <option value="Facilities">Facilities</option>
                   <option value="Student Life">Student Life</option>
                   <option value="Other">Other</option>
               </select>
           </div>
           <div class="form-group star-rating">
                <label>Your Rating:</label>
                <div class="stars">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
           </div>
           <input type="hidden" name="rating" id="ratingInput" value="0">
           <textarea name="feedback" placeholder="Tell us what you think..." required></textarea>
           <button type="submit" class="btn-submit">Submit Feedback</button>
        </form>
      </div>
    </div>
  </main>
<script src="script/dashboard.js" defer></script>
</body>
</html>
