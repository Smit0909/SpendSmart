<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['to_email'])) {
    header('Location: login.php');
    exit();
}

$emptyError = $samePassword = '';
$email = $_SESSION['to_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $cpassword = $_POST['confirm-password'];

    if ($password == "" || $cpassword == "") {
        $emptyError = "All fields are mandatory.";
    } else {
        if ($password == $cpassword) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $sql = "UPDATE users set password ='$password' WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result) {
                $_SESSION['isPasswordUpdated'] = TRUE;
                unset($_SESSION['to_email']);
                unset($_SESSION['otp']);
                header('Location: login.php');
                exit;
            }
        } else {
            $samePassword = "Password and confirm password should be same.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - Reset Password</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/fp.css">
</head>

<body>
    <div class="container">
        <div class="box">
            <section class="left-container">
                <div class="form-container">
                    <h2>Reset Password</h2>
                    <p class="emptyfield">All fields are mandatory.</p>
                    <p class="samePassword">Password and confirm password should be same.</p>
                    <!-- PHP Error Handling -->
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
                    <p class="error" style="display: <?= $samePassword ? 'block' : 'none'; ?>;"><?= $samePassword; ?></p>

                    <form method="POST" action="resetPassword.php" id="PasswordForm">
                        <div>
                            <label for="password">New Password:</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div>
                            <label for="password">Confirm Password:</label>
                            <input type="password" id="confirm-password" name="confirm-password" required>
                        </div>

                        <div>
                            <button type="submit" name="submit" id="submit-btn">Reset Password</button>
                        </div>
                    </form>
                </div>
            </section>

            <div class="right-container">
                <img src="Images/21586028_Na_Nov_15.svg" alt="bg">
            </div>

        </div>
    </div>
    <script src="js/resetPassword.js"></script>
</body>

</html>