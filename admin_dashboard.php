<?php
  require_once("api/database.php");
  session_start();

  // Security Check: Only allow users with the 'admin' role to access this page.
  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      // If the user is not an admin, redirect them to the login page.
      header("Location: login.php");
      exit();
  }

  // Fetch all feedback from the database, joining with the users_info table to get usernames.
  // Order the results to show unresolved feedback first.
  $sql = "SELECT f.*, u.username FROM feedback f JOIN users_info u ON f.user_id = u.id ORDER BY f.status ASC, f.date DESC";
  $result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles/dashboard.css">
</head>
<body>
  <?php include 'sidebar.php'; ?>

  <main>
    <header>
      <h1>Admin Dashboard</h1>
      <a href="logout.php" class="logout-btn">Logout</a>
    </header>

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
                                <!-- Only show the "Resolve" button if the status is "Unresolved" -->
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
</body>
</html>
