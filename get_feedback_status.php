
<?php
header('Content-Type: application/json');
include("api/database.php");

// Get total feedback
$sql_total = "SELECT COUNT(*) AS total FROM feedback";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total = (int)$row_total['total'];

// Get solved feedback
$sql_solved = "SELECT COUNT(*) AS solved FROM feedback WHERE status = 'Resolved'";
$result_solved = mysqli_query($conn, $sql_solved);
$row_solved = mysqli_fetch_assoc($result_solved);
$solved = (int)$row_solved['solved'];

// Calculate percentage safely
$solvedPercent = ($total > 0) ? round(($solved / $total) * 100, 2) : 0;

// Return JSON
echo json_encode([
    "total" => $total,
    "solved" => $solved,
    "solvedPercent" => $solvedPercent
]);
?>