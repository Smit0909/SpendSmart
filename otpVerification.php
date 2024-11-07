<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['isOTPSent']))
{
    header('Location: login.php');
    exit;
}

$emptyError = $incorrectOTP = '';
$email = $_SESSION['to_email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $stored_otp = $_SESSION['otp'];

    if ($otp == "") {
        $emptyError = "Please enter your otp.";
    } else {
        if ($otp == $stored_otp) {
            header('Location: resetPassword.php');
            exit();
        } else {
            $incorrectOTP = "Incorrect OTP. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - OTP Verification</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/fp.css">
</head>

<body>
    <div class="container">
        <div class="box">
            <section class="left-container">
                <div class="form-container">
                    <h2>OTP Verification</h2>
                    <p class="emptyfield">Please enter OTP.</p>
                    <!-- PHP Error Handling -->
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
                    <p class="error" style="display: <?= $incorrectOTP ? 'block' : 'none'; ?>;"><?= $incorrectOTP; ?></p>
                    <?php
                    if (isset($_SESSION['isOTPSent']) && $_SESSION['isOTPSent']) {
                        echo "<p class='OTPSuccess'>OTP sent to you email {$email}.</p>";
                        $_SESSION['isOTPSent'] = FALSE;
                    }
                    ?>
                    
                    <form method="POST" action="otpVerification.php" id="OTPForm">
                        <div>
                            <label for="otp">OTP:</label>
                            <input type="number" id="otp" name="otp" required>
                        </div>

                        <div>
                            <button type="submit" name="submit" id="submit-btn">Verify OTP</button>
                        </div>
                    </form>
                </div>
            </section>

            <div class="right-container">
                <img src="Images/21586028_Na_Nov_15.svg" alt="bg">
            </div>

        </div>
    </div>
    <script src="js/otpVerification.js"></script>
</body>

</html>