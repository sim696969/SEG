<?php
// Secure and reusable database connection function
function getDbConnection() {
    static $conn; // Use a static variable to avoid multiple connections

    if ($conn === null) {
        $db_server = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "feedback_management_system_db";

        try {
            $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
            if (!$conn) {
                error_log("Database connection failed: " . mysqli_connect_error());
                die("A database error occurred. Please try again later.");
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Database connection exception: " . $e->getMessage());
            die("Could not connect to the database.");
        }
    }
    return $conn;
}

// Get the connection for use in your files
$conn = getDbConnection();
?>