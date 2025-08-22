<?php
  require_once("api/database.php");
  session_start();
  if (!isset($_SESSION['user_id'])) {
      header("Location: login.php");
      exit();
  }

  $user_id = $_SESSION['user_id'];
  $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

  // Debug: Check feedback table structure and sample data
  $debug_sql = "SELECT * FROM feedback LIMIT 3";
  $debug_result = mysqli_query($conn, $debug_sql);
  if ($debug_result) {
      echo "<!-- Debug: Sample feedback data: -->";
      while ($debug_row = mysqli_fetch_assoc($debug_result)) {
          echo "<!-- " . json_encode($debug_row) . " -->";
      }
  }
  
  // Fetch feedback data for the calendar
  if ($is_admin) {
      // For admin: get all feedback submissions grouped by date
      $feedback_sql = "SELECT DATE(date) as feedback_date, COUNT(*) as count FROM feedback GROUP BY DATE(date)";
      $feedback_result = mysqli_query($conn, $feedback_sql);
      if (!$feedback_result) {
          echo "<!-- Debug: Admin query error: " . mysqli_error($conn) . " -->";
      }
      $feedback_by_date = [];
      while ($row = mysqli_fetch_assoc($feedback_result)) {
          $feedback_by_date[$row['feedback_date']] = $row['count'];
      }
  } else {
      // For student: get their own feedback submissions
      $feedback_sql = "SELECT DATE(date) as feedback_date, category, rating FROM feedback WHERE user_id = ? ORDER BY date DESC";
      $stmt = mysqli_prepare($conn, $feedback_sql);
      if (!$stmt) {
          echo "<!-- Debug: Student query prepare error: " . mysqli_error($conn) . " -->";
      }
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      $execute_result = mysqli_stmt_execute($stmt);
      if (!$execute_result) {
          echo "<!-- Debug: Student query execute error: " . mysqli_stmt_error($stmt) . " -->";
      }
      $feedback_result = mysqli_stmt_get_result($stmt);
      $feedback_by_date = [];
      while ($row = mysqli_fetch_assoc($feedback_result)) {
          if (!isset($feedback_by_date[$row['feedback_date']])) {
              $feedback_by_date[$row['feedback_date']] = [];
          }
          $feedback_by_date[$row['feedback_date']][] = [
              'category' => $row['category'],
              'rating' => $row['rating']
          ];
      }
  }

  // Debug: Show what data was fetched
  echo "<!-- Debug: User ID: $user_id, Is Admin: " . ($is_admin ? 'true' : 'false') . " -->";
  echo "<!-- Debug: Feedback data: " . json_encode($feedback_by_date) . " -->";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calendar</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <?php if ($is_admin): ?>
  <link rel="stylesheet" href="styles/admin.css">
  <?php else: ?>
  <link rel="stylesheet" href="styles/student.css">
  <?php endif; ?>
</head>
<body class="<?php echo $is_admin ? 'admin-theme' : 'student-theme'; ?>">
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
        <div class="header-left">
            <button id="mainSidebarToggle" class="main-sidebar-toggle" title="Show Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                    <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                </svg>
            </button>
            <h1>Calendar</h1>
            <?php if ($is_admin): ?>
                <p>View feedback submission statistics by date</p>
            <?php else: ?>
                <p>Track your feedback submission history</p>
            <?php endif; ?>
        </div>
        <div class="header-right">
            <div class="search-bar">
                <input type="text" placeholder="Search events...">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#5f6368"><path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/></svg>
            </div>
            <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
            <button id="feedbackBtn" class="new-feedback-btn">+ New Feedback</button>
            <?php endif; ?>
        </div>
    </header>

    <div class="calendar-container">
        <div class="calendar-header">
            <button id="prevMonthBtn" class="nav-btn">&lt;</button>
            <h2 id="monthYear"></h2>
            <button id="nextMonthBtn" class="nav-btn">&gt;</button>
        </div>
        <div class="calendar-weekdays">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>
        <div class="calendar-grid" id="calendarGrid"></div>
        
        <!-- Calendar Legend -->
        <div class="calendar-legend">
            <?php if ($is_admin): ?>
                <div class="legend-item">
                    <span class="legend-dot admin-feedback"></span>
                    <span>Feedback submissions</span>
                </div>
            <?php else: ?>
                <div class="legend-item">
                    <span class="legend-dot student-feedback"></span>
                    <span>Your feedback</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot high-rating"></span>
                    <span>High rating (4-5 stars)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot low-rating"></span>
                    <span>Low rating (1-3 stars)</span>
                </div>
            <?php endif; ?>
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

<!-- Pass feedback data to JavaScript -->
<script>
const feedbackData = <?php echo json_encode($feedback_by_date); ?>;
const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;

// Debug: Log the variables in console
console.log('PHP Variables:', { feedbackData, isAdmin });
</script>

<script src="script/calendar.js" defer></script>
<script src="script/dashboard.js" defer></script>
<script src="script/sidebar.js" defer></script>
</body>
</html>
