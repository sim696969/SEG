<?php
  require_once("api/database.php");
  date_default_timezone_set('Asia/Kuala_Lumpur');
  session_start();

  // Security: Redirect to login if user is not logged in
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }

  // Handle feedback form submission
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
      $user_id = $_SESSION['user_id'];
      $category = filter_input(INPUT_POST, "category", FILTER_SANITIZE_SPECIAL_CHARS);
      $rating = filter_input(INPUT_POST, "rating", FILTER_VALIDATE_INT);
      $feedback = filter_input(INPUT_POST, "feedback", FILTER_SANITIZE_SPECIAL_CHARS);

      if (!empty($feedback) && $rating > 0 && !empty($category)) {
          $sql = "INSERT INTO feedback (user_id, category, rating, feedback) VALUES (?, ?, ?, ?)";
          $stmt = mysqli_prepare($conn, $sql);
          mysqli_stmt_bind_param($stmt, "isis", $user_id, $category, $rating, $feedback);
          
          if (mysqli_stmt_execute($stmt)) {
              $_SESSION['feedback_status'] = ['message' => 'Feedback submitted successfully!', 'type' => 'success'];
          } else {
              $_SESSION['feedback_status'] = ['message' => 'Failed to submit feedback. Please try again.', 'type' => 'error'];
          }
      } else {
           $_SESSION['feedback_status'] = ['message' => 'Please select a category, provide a rating, and write a message.', 'type' => 'error'];
      }
      header("Location: student_dashboard.php");
      exit();
  }

  // Fetch recent feedback
  $recent_feedback_sql = "SELECT f.*, u.username FROM feedback f JOIN users_info u ON f.user_id = u.id ORDER BY f.date DESC LIMIT 5";
  $recent_feedback_result = mysqli_query($conn, $recent_feedback_sql);

  // Fetch statistics
  $total_feedback = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM feedback"))['total'];
  $total_resolved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM feedback WHERE status = 'Resolved'"))['total'];
  $total_unresolved = $total_feedback - $total_resolved;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
      <div class="header-left">
          <h1>Hello <?php echo htmlspecialchars(explode(' ', $_SESSION['username'])[0]); ?>,</h1>
          <p>Today is <?php echo date("d F, Y"); ?></p>
      </div>
      <div class="header-right">
          <div class="search-bar">
              <input type="text" placeholder="Search...">
              <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
          </div>
          <button id="feedbackBtn" class="new-feedback-btn">+ New Feedback</button>
      </div>
    </header>

    <div class="stats-container">
        <div class="stats-card">
            <img src="pic/chat.gif" alt="Total Feedback">
            <div class="stats-info">
                <p>Total Feedback</p>
                <span><?php echo $total_feedback; ?></span>
            </div>
        </div>
        <div class="stats-card">
            <img src="pic/verified.gif" alt="Resolved">
            <div class="stats-info">
                <p>Resolved</p>
                <span><?php echo $total_resolved; ?></span>
            </div>
        </div>
        <div class="stats-card">
            <img src="pic/warning.gif" alt="Unresolved">
            <div class="stats-info">
                <p>Unresolved</p>
                <span><?php echo $total_unresolved; ?></span>
            </div>
        </div>
        <div class="stats-card">
            <img src="pic/clock.gif" alt="Pending">
            <div class="stats-info">
                <p>Pending</p>
                <span><?php echo $total_unresolved; ?></span>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div class="my-courses">
            <h2>My Courses</h2>
            <div class="course-list">
                <div class="course-card">
                    <div class="course-icon" style="background-color: #e7f2ff;"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#4a6cfd"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480q0-33 23.5-56.5T240-680l240-180 240 180q23 17 23.5 43.5T720-560v480q0 33-23.5 56.5T640-0H160q-33 0-56.5-23.5T80-80Zm320-420Z"/></svg></div>
                    <div class="course-info"><h3>Introduction to Programing</h3><a href="#">view</a></div>
                </div>
                <div class="course-card">
                    <div class="course-icon" style="background-color: #e5f8f0;"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#28a745"><path d="M400-40 280-160l80-80 40 40-80 80 80 80-40 40Zm160 0-40-40 80-80-80-80 40-40 120 120-120 120ZM120-200v-560h720v560H120Zm80-80h560v-400H200v400Zm0 0v-400 400Z"/></svg></div>
                    <div class="course-info"><h3>Discrete Mathematics</h3><a href="#">view</a></div>
                </div>
                <div class="course-card">
                    <div class="course-icon" style="background-color: #fff4e5;"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#ffc107"><path d="M480-240q-50 0-85-35t-35-85q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35ZM211-120q-21-21-21-49.5t21-49.5l112-112q14-14 34-19t41-1q36 27 78 43t89 19q47 0 89-16t78-43q21-4 41 1t34 19l112 112q21 21 21 49.5T749-120q-21 21-49.5 21T650-120l-112-112q-11-11-26-11t-26 11q-34 29-76 45t-88 16q-46 0-88-16t-76-45q-11-11-26-11t-26 11L260-120q-21 21-49.5 21T211-120ZM480-480Z"/></svg></div>
                    <div class="course-info"><h3>Falsafah dan Isu Semasa</h3><a href="#">view</a></div>
                </div>
            </div>
        </div>
        <div class="recent-feedback">
            <h2>Recent Feedback</h2>
            <div class="feedback-list">
                <?php if($recent_feedback_result && mysqli_num_rows($recent_feedback_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($recent_feedback_result)): 
                        $initial = strtoupper(substr($row['username'], 0, 1));
                    ?>
                        <div class="feedback-item-card">
                            <div class="user-avatar"><?php echo $initial; ?></div>
                            <div class="feedback-details">
                                <p class="feedback-user-name"><?php echo htmlspecialchars($row['username']); ?></p>
                                <p class="feedback-text-snippet"><?php echo htmlspecialchars(substr($row['feedback'], 0, 50)) . '...'; ?></p>
                            </div>
                            <div class="feedback-date-card"><?php echo date("d M Y", strtotime($row["date"])); ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No recent feedback to display.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="feedback-analytics">
            <h2>Feedback Analytics</h2>
            <div class="chart-container"><canvas id="feedbackChart"></canvas></div>
        </div>
    </div>
    
    <!-- Feedback Modal -->
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

<script>
// Pass PHP data to JavaScript for the chart
const unresolvedCount = <?php echo $total_unresolved; ?>;
const resolvedCount = <?php echo $total_resolved; ?>;
</script>
<script src="script/dashboard.js" defer></script>
</body>
</html>
