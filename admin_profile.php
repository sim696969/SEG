<?php
  require_once("api/database.php");
  session_start();

  // Security Check: Only admins can access this page
  if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
      header("Location: login.php");
      exit();
  }

  $user_id = $_SESSION['user_id'];

  // Fetch the current admin's details
  $user_sql = "SELECT username, email, stu_id FROM users_info WHERE id = ?";
  $stmt_user = mysqli_prepare($conn, $user_sql);
  mysqli_stmt_bind_param($stmt_user, "i", $user_id);
  mysqli_stmt_execute($stmt_user);
  $user_result = mysqli_stmt_get_result($stmt_user);
  $user = mysqli_fetch_assoc($user_result);

  // Fetch admin-specific statistics
  $total_feedback = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM feedback"))['total'];
  $total_resolved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM feedback WHERE status = 'Resolved'"))['total'];
  $total_unresolved = $total_feedback - $total_resolved;
  $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users_info WHERE role != 'admin'"))['total'];
  
  // Fetch recent admin actions (resolved feedback)
  $recent_actions_sql = "SELECT f.*, u.username FROM feedback f JOIN users_info u ON f.user_id = u.id WHERE f.status = 'Resolved' ORDER BY f.date DESC LIMIT 5";
  $recent_actions_result = mysqli_query($conn, $recent_actions_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile - ViTrox College</title>
  <link rel="stylesheet" href="styles/dashboard.css">
  <link rel="stylesheet" href="styles/admin.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-theme">
  <?php include 'sidebar.php'; ?>

  <main>
    <header class="dashboard-header">
        <div class="header-left">
            <h1>Admin Profile</h1>
            <p>Administrative Dashboard & System Overview</p>
        </div>
        <div class="header-right">
            <div class="admin-status">
                <span class="status-badge admin">Administrator</span>
                <span class="status-badge online">Online</span>
            </div>
        </div>
    </header>

    <div class="admin-profile-container">
        <!-- Admin Profile Card -->
        <div class="admin-profile-card">
            <div class="admin-profile-header">
                <div class="admin-avatar">
                    <div class="avatar-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" height="48px" viewBox="0 -960 960 960" width="48px" fill="#38bdf8">
                            <path d="M480-120q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-200v-80h80v80H160Zm0-160v-80h80v80H160Zm0-160v-80h80v80H160Zm160-160v-80h80v80H320Zm0 480v-80h80v80H320Zm160-480v-80h80v80H480Zm0 480v-80h80v80H480Zm160-480v-80h80v80H640Zm0 480v-80h80v80H640Zm160-320v-80h80v80H800Zm0-160v-80h80v80H800Zm0 320v-80h80v80H800ZM80-120v-720q0-33 23.5-56.5T160-920h640q33 0 56.5 23.5T880-840v720q0 33-23.5 56.5T800-40H160q-33 0-56.5-23.5T80-120Zm80-80h640v-720H160v720Zm0 0v-720 720Z"/>
                        </svg>
                    </div>
                    <div class="admin-badge">ADMIN</div>
                </div>
                <div class="admin-info">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p class="admin-email"><?php echo htmlspecialchars($user['email']); ?></p>
                    <p class="admin-id">Admin ID: <?php echo htmlspecialchars($user['stu_id']); ?></p>
                    <div class="admin-permissions">
                        <span class="permission-badge">Full Access</span>
                        <span class="permission-badge">User Management</span>
                        <span class="permission-badge">System Control</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Statistics Grid -->
        <div class="admin-stats-grid">
            <div class="stat-card admin-stat">
                <div class="stat-icon users">
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#38bdf8">
                        <path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q62 0 126 15.5T612-378q29 15 46.5 43.5T676-272v112H40Zm720 0v-112q0-34-17.5-62.5T720-378q-62-31-126-46.5T480-440q-62 0-126 15.5T228-378q-29 15-46.5 43.5T164-272v112h596ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm240 0q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM480-560q-33 0-56.5-23.5T400-640q0-33 23.5-56.5T480-720q33 0 56.5 23.5T560-640q0 33-23.5 56.5T480-560Zm0 240q-33 0-56.5-23.5T400-400q0-33 23.5-56.5T480-480q33 0 56.5 23.5T560-400q0 33-23.5 56.5T480-320Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Total Users</h3>
                    <span class="stat-number"><?php echo $total_users; ?></span>
                    <p class="stat-description">Registered Students</p>
                </div>
            </div>

            <div class="stat-card admin-stat">
                <div class="stat-icon feedback">
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#10b981">
                        <path d="M240-400h480v-80H240v80Zm0 160h480v-80H240v80Zm0-320h480v-80H240v80ZM80-80v-800h800v800H80Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Total Feedback</h3>
                    <span class="stat-number"><?php echo $total_feedback; ?></span>
                    <p class="stat-description">All Submissions</p>
                </div>
            </div>

            <div class="stat-card admin-stat">
                <div class="stat-icon resolved">
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#f59e0b">
                        <path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Resolved</h3>
                    <span class="stat-number"><?php echo $total_resolved; ?></span>
                    <p class="stat-description">Issues Fixed</p>
                </div>
            </div>

            <div class="stat-card admin-stat">
                <div class="stat-icon pending">
                    <svg xmlns="http://www.w3.org/2000/svg" height="32px" viewBox="0 -960 960 960" width="32px" fill="#ef4444">
                        <path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                    </svg>
                </div>
                <div class="stat-content">
                    <h3>Pending</h3>
                    <span class="stat-number"><?php echo $total_unresolved; ?></span>
                    <p class="stat-description">Needs Attention</p>
                </div>
            </div>
        </div>

        <!-- Admin Analytics & Recent Actions -->
        <div class="admin-content-grid">
            <!-- System Performance Chart -->
            <div class="admin-chart-section">
                <h3>System Performance Overview</h3>
                <div class="chart-container">
                    <canvas id="adminPerformanceChart"></canvas>
                </div>
            </div>

            <!-- Recent Admin Actions -->
            <div class="admin-actions-section">
                <h3>Recent Administrative Actions</h3>
                <div class="actions-list">
                    <?php if($recent_actions_result && mysqli_num_rows($recent_actions_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($recent_actions_result)): ?>
                            <div class="action-item">
                                <div class="action-icon resolved">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px" fill="#10b981">
                                        <path d="m424-296 282-282-56-56-226 226-114-114-56 56 170 170Zm56 216q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Z"/>
                                    </svg>
                                </div>
                                <div class="action-details">
                                    <p class="action-text">Resolved feedback from <strong><?php echo htmlspecialchars($row['username']); ?></strong></p>
                                    <p class="action-category"><?php echo htmlspecialchars($row['category']); ?></p>
                                    <span class="action-date"><?php echo date("d M Y, h:i A", strtotime($row["date"])); ?></span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-actions">No recent administrative actions to display.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
  </main>

<script>
// Create admin performance chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('adminPerformanceChart');
    
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Users', 'Feedback', 'Resolved', 'Pending'],
                datasets: [{
                    label: 'System Metrics',
                    data: [<?php echo $total_users; ?>, <?php echo $total_feedback; ?>, <?php echo $total_resolved; ?>, <?php echo $total_unresolved; ?>],
                    backgroundColor: [
                        'rgba(56, 189, 248, 0.8)',   // Blue for users
                        'rgba(16, 185, 129, 0.8)',   // Green for feedback
                        'rgba(245, 158, 11, 0.8)',   // Amber for resolved
                        'rgba(239, 68, 68, 0.8)'     // Red for pending
                    ],
                    borderColor: [
                        'rgba(56, 189, 248, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        titleColor: '#f1f5f9',
                        bodyColor: '#f1f5f9',
                        borderColor: '#38bdf8',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(71, 85, 105, 0.3)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(71, 85, 105, 0.3)'
                        },
                        ticks: {
                            color: '#94a3b8'
                        }
                    }
                }
            }
        });
    }
});
</script>
</body>
</html>
