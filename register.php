<?php
session_start();
include 'connection.php';

$emptyError = $emailExist = $emailError = $samePassword = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['confirm-password'];

    if (empty($username) || empty($email) || empty($password) || empty($cpassword)) {
        $emptyError = "All fields are mandatory.";
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $sqlSearch = "SELECT * FROM users WHERE email = '$email'";
            $result = $conn->query($sqlSearch);
            if ($result->num_rows > 0) {
                $emailExist = "Email already exist. Please log in.";
            } else {
                if ($password != $cpassword) {
                    $samePassword = "Password and confirm password should be same.";
                } else {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
                    if ($conn->query($sql) === TRUE) {
                        $_SESSION['SignUp'] = TRUE;
                        header("Location: login.php");
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }
            }
        } else {
            $emailError = "Please enter valid email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - Sign Up</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/register.css">
</head>

<body>
    <div class="container">
        <div class="box">
            <div class="left-container">
                <img src="Images/10783928_19198913.svg" alt="bg">
            </div>
            <section class="right-container">
                <div class="form-container">
                    <h2>Sign Up</h2>
                    <p class="emptyfield">All fields are mandatory.</p>
                    <p class="emailexist">Email already exist. Please log in.</p>
                    <p class="validemail">Please enter valid email address.</p>
                    <p class="samePassword">Password and confirm password should be same.</p>   
                    <!-- PHP Error Handling -->
                    <p class="error" style="display: <?= $emailError ? 'block' : 'none'; ?>;"><?= $emailError; ?></p>
                    <p class="error" style="display: <?= $emailExist ? 'block' : 'none'; ?>;"><?= $emailExist; ?></p>
                    <p class="error" style="display: <?= $samePassword ? 'block' : 'none'; ?>;"><?= $samePassword; ?></p>
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
        
                    <form method="POST" action="register.php" id="signUpForm">
                        <div>
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div>
                            <label for="confirm-password">Confirm Password:</label>
                            <input type="password" id="confirm-password" name="confirm-password" required>
                        </div>
                        <div>
                            <button type="submit" name="submit" id="submit-btn">Sign Up</button>
                        </div>
                        <div>
                            <p id="login-link">Already have an account? <a href="login.php">Login</a></p>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
    <script src="js/register.js"></script>
</body>

</html>