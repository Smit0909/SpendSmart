<?php
session_start();
include 'connection.php';



$emptyError = $notEmailError = $emailError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    if ($email == "") {
        $emptyError = "Please enter your email address.";
    } else {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $sql = "SELECT * FROM users WHERE email='$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $_SESSION['to_email'] = $email;
                header('Location: sendOTP.php');
                exit();
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
    <title>SpendSmart - Forgot Password</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/fp.css">
</head>

<body>
    <div class="container">
        <div class="box">
            <section class="left-container">
                <div class="form-container">
                    <h2>Password Recovery</h2>
                    <p class="emptyfield">Please enter your email address.</p>
                    <p class="validemail">Please enter valid email address.</p>
                    <!-- PHP Error Handling -->
                    <p class="error" style="display: <?= $emailError ? 'block' : 'none'; ?>;"><?= $emailError; ?></p>
                    <p class="error" style="display: <?= $notEmailError ? 'block' : 'none'; ?>;"><?= $notEmailError; ?></p>
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
                    
                    <form method="POST" action="fp.php" id="fpForm">
                        <div>
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <div>
                            <button type="submit" name="submit" id="submit-btn">Send OTP</button>
                        </div>
                    </form>
                </div>
            </section>

            <div class="right-container">
                <img src="Images/21586028_Na_Nov_15.svg" alt="bg">
            </div>

        </div>
    </div>
    <script src="js/fp.js"></script>
</body>

</html>