<?php
// Simple test file to check if everything is working
echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working!</p>";

// Test database connection
try {
    require_once 'api/database.php';
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test a simple query
    $test_query = "SELECT 1 as test";
    $result = mysqli_query($conn, $test_query);
    if ($result) {
        echo "<p style='color: green;'>✓ Database query successful!</p>";
    } else {
        echo "<p style='color: red;'>✗ Database query failed!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Check PHP info
echo "<h2>PHP Information:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Check if required extensions are loaded
$required_extensions = ['mysqli', 'session'];
echo "<h2>Required Extensions:</h2>";
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ $ext extension loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ $ext extension NOT loaded</p>";
    }
}
?>
