<?php
session_start();
include 'connection.php';

$emptyError = $passwordError = $notEmailError = $emailError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($email == "" || $password == "") {
        $emptyError = "All fields are mandatory.";
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $passwordError = "Invalid Password.";
                }
            } else {
                $notEmailError = "Email doesn't exist.";
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
    <title>SpendSmart - Login</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container">
        <div class="box">
            <section class="left-container">
                <div class="form-container">
                    <h2>Login</h2>
                    <p class="emptyfield">All fields are mandatory.</p>
                    <p class="emailnotexist">Email doesn't exist.</p>
                    <p class="invalidPassword">Invalid Password.</p>
                    <p class="validemail">Please enter valid email address.</p>
                    <!-- PHP Error Handling -->
                    <p class="error" style="display: <?= $emailError ? 'block' : 'none'; ?>;"><?= $emailError; ?></p>
                    <p class="error" style="display: <?= $notEmailError ? 'block' : 'none'; ?>;"><?= $notEmailError; ?></p>
                    <p class="error" style="display: <?= $passwordError ? 'block' : 'none'; ?>;"><?= $passwordError; ?></p>
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
                    <?php
                    if (isset($_SESSION['SignUp']) && $_SESSION['SignUp']) {
                        echo "<p class='signUpSuccess'>You're signed up! Now, log in with your credentials.</p>";
                        unset($_SESSION['SignUp']);
                    }
                    if (isset($_SESSION['isPasswordUpdated']) && $_SESSION['isPasswordUpdated']) {
                        echo "<p class='resetPwdSuccess'>Password reset successfully. Please login.</p>";
                        unset($_SESSION['isPasswordUpdated']);
                    }

                    ?>
                    <form method="POST" action="login.php" id="loginForm">
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div>
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div>
                            <a href="fp.php">Forgot Password?</a>
                        </div>
                        <div>
                            <button type="submit" name="submit" id="submit-btn">Login</button>
                        </div>
                        <div>
                            <p id="signup-link">Don't have an account? <a href="register.php">Sign Up</a></p>
                        </div>
                    </form>
                </div>
            </section>

            <div class="right-container">
                <img src="Images/21586028_Na_Nov_15.svg" alt="bg">
            </div>

        </div>
    </div>
    <script src="js/login.js"></script>
</body>

</html>