<?php
    // This line MUST be at the very top of the file to connect to the database first.
    require_once("api/database.php"); 
    $error = "";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $stu_id = filter_input(INPUT_POST, "stu_id", FILTER_SANITIZE_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
        $phone_num = filter_input(INPUT_POST, "phone_num", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
        $password = $_POST["password"];

        if (empty($username) || empty($email) || empty($password)) {
            $error = "Please fill in all required fields.";
        } elseif (!$email) {
            $error = "Invalid email format.";
        } else {
            // This code will now work because $conn is a valid connection.
            $check_sql = "SELECT id FROM users_info WHERE username=? OR email=?";
            $stmt = mysqli_prepare($conn, $check_sql);
            
            // This check prevents the error you are seeing
            if ($stmt === false) {
                // Log the actual SQL error for debugging
                error_log("SQL Prepare Failed: " . mysqli_error($conn));
                $error = "An unexpected error occurred. Please try again later.";
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $username, $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $error = "Username or Email is already registered!";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $role = 'user';

                    $insert_sql = "INSERT INTO users_info (stu_id, username, email, phone_num, password, role) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt_insert = mysqli_prepare($conn, $insert_sql);
                    mysqli_stmt_bind_param($stmt_insert, "ssssss", $stu_id, $username, $email, $phone_num, $hash, $role);

                    if (mysqli_stmt_execute($stmt_insert)) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "Registration failed. Please try again.";
                    }
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="styles/Login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h1>Register an Account</h1>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" placeholder="Student ID" required name="stu_id">
                <i class='bx bxs-id-card'></i>
            </div>
            <div class="input-box">
                <input type="text" placeholder="Username" required name="username">
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="tel" placeholder="Phone Number" required name="phone_num">
                <i class='bx bxs-phone'></i>
            </div>
            <div class="input-box">
                <input type="email" placeholder="Email" required name="email">
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" placeholder="Password" required name="password">
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" class="btn">Register</button>
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
