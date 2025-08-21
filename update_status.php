<?php
require_once("api/database.php");
session_start();

// Security Check: Only admins can perform this action.
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied: You do not have permission to perform this action.");
}

// Check if the form was submitted with a feedback_id.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    // Sanitize the input to ensure it's an integer.
    $feedback_id = filter_input(INPUT_POST, 'feedback_id', FILTER_VALIDATE_INT);

    if ($feedback_id) {
        // Use a prepared statement to prevent SQL injection.
        $sql = "UPDATE feedback SET status = 'Resolved' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $feedback_id);
        mysqli_stmt_execute($stmt);
    }
}

// Redirect the admin back to the dashboard after the action is complete.
header("Location: admin_dashboard.php");
exit();
?>
