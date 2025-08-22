<?php
  require_once("api/database.php");
  session_start();

  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }

  $user_id = $_SESSION['user_id'];

  // Fetch the current user's details
  $user_sql = "SELECT username, email, stu_id FROM users_info WHERE id = ?";
  $stmt_user = mysqli_prepare($conn, $user_sql);
  mysqli_stmt_bind_param($stmt_user, "i", $user_id);
  mysqli_stmt_execute($stmt_user);
  $user_result = mysqli_stmt_get_result($stmt_user);
  $user = mysqli_fetch_assoc($user_result);

  // Fetch all feedback submitted by the current user
  $feedback_sql = "SELECT * FROM feedback WHERE user_id = ? ORDER BY date DESC";
  $stmt_feedback = mysqli_prepare($conn, $feedback_sql);
  mysqli_stmt_bind_param($stmt_feedback, "i", $user_id);
  mysqli_stmt_execute($stmt_feedback);
  $feedback_result = mysqli_stmt_get_result($stmt_feedback);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/student.css">
</head>
<body class="student-theme">
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
        <div class="header-left">
            <button id="mainSidebarToggle" class="main-sidebar-toggle" title="Show Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                    <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                </svg>
            </button>
            <h1>My Profile</h1>
            <p>View your information and feedback history.</p>
        </div>
        <div class="header-right">
            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
            <button id="feedbackBtn" class="new-feedback-btn">+ New Feedback</button>
            <?php endif; ?>
        </div>
    </header>

    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <span>Student ID: <?php echo htmlspecialchars($user['stu_id']); ?></span>
            </div>
        </div>

        <div class="feedback-history">
            <h2>Your Feedback History</h2>
            <div class="feedback-list">
                <?php if($feedback_result && mysqli_num_rows($feedback_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($feedback_result)): ?>
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
        </div>
    </div>

    <!-- Feedback Modal - Only for non-admin users -->
    <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
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
    <?php endif; ?>
  </main>
<script src="script/dashboard.js" defer></script>
<script src="script/sidebar.js" defer></script>
</body>
</html>
