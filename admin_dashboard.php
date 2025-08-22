<?php
  require_once("api/database.php");
  session_start();

  // Security Check: Only allow users with the 'admin' role to access this page.
  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      header("Location: login.php");
      exit();
  }

  // Fetch all feedback for the main table
  $sql = "SELECT f.*, u.username FROM feedback f JOIN users_info u ON f.user_id = u.id ORDER BY f.status ASC, f.date DESC";
  $result = mysqli_query($conn, $sql);

  // Fetch data for the category analytics chart
  $category_sql = "SELECT category, COUNT(*) as count FROM feedback GROUP BY category";
  $category_result = mysqli_query($conn, $category_sql);

  $categories = [];
  $category_counts = [];
  if ($category_result) {
      while($row = mysqli_fetch_assoc($category_result)) {
          $categories[] = $row['category'];
          $category_counts[] = $row['count'];
      }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-theme">
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
        <div class="header-left">
            <button id="mainSidebarToggle" class="main-sidebar-toggle" title="Show Sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="currentColor">
                    <path d="M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z"/>
                </svg>
            </button>
            <h1>Admin Dashboard</h1>
            <p>Manage all student feedback.</p>
        </div>
    </header>

    <div class="admin-analytics-container">
        <div class="admin-chart-container">
            <h2>Feedback by Category</h2>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

    <div class="admin-table-container">
        <h2>All User Feedback</h2>
        <table class="feedback-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Feedback</th>
                    <th>Category</th>
                    <th>Rating</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['feedback']); ?></td>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                            <td><?php echo htmlspecialchars($row['rating']); ?> ★</td>
                            <td><?php echo date("d M Y", strtotime($row["date"])); ?></td>
                            <td>
                                <span class="feedback-status <?php echo strtolower($row['status']); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Unresolved'): ?>
                                    <form action="update_status.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="resolve-btn">Mark as Resolved</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No feedback has been submitted yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
// Pass the category data from PHP to JavaScript for the chart
const categoryLabels = <?php echo json_encode($categories); ?>;
const categoryData = <?php echo json_encode($category_counts); ?>;
</script>
<script src="script/admin_dashboard.js" defer></script>
<script src="script/sidebar.js" defer></script>
</body>
</html>
