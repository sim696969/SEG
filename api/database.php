<?php
// This function establishes a secure connection to the database.
function getDbConnection() {
    // static variable ensures the connection is made only once per request.
    static $conn; 

    if ($conn === null) {
        $db_server = "localhost";
        $db_user = "root";
        $db_pass = "";
        $db_name = "feedback_management_system_db";

        try {
            $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
            if (!$conn) {
                // Log errors for the developer instead of showing them to the user.
                error_log("Database connection failed: " . mysqli_connect_error());
                die("Could not connect to the database. Please try again later.");
            }
        } catch (mysqli_sql_exception $e) {
            error_log("Database connection exception: " . $e->getMessage());
            die("A database error occurred.");
        }
    }
    return $conn;
}

// Make the connection variable available to any file that includes this one.
$conn = getDbConnection();
?>
