<?php

session_start();
include "connection.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$emptyError = '';

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$id'";
$result = $conn->query($sql);

if ($result) {
    $arrdata = $result->fetch_assoc();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

if (isset($_POST['submit']) && $_SERVER["REQUEST_METHOD"] == "POST") {

    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $reference = $_POST['reference'];

    if (empty($title) || empty($amount) || empty($date) || empty($reference)) {
        $emptyError = "All fields are mandatory.";
    } else {

        $sqlinsert = "INSERT INTO incomes (user_id, title, amount, description, date) VALUES ('$id', '$title', '$amount', '$reference', '$date')";

        if (!$conn->query($sqlinsert)) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - Incomes</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/incomes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="container">
        <div class="left-box">
            <div class="profile-container">
                <img src="Images/portrait-man-cartoon-style.jpg" alt="profilepic">
                <div class="profile-info">
                    <?php echo "<h2>{$arrdata['username']}</h2>"; ?>
                    <?php
                    $sql_balance = "SELECT 
                                    (SELECT COALESCE(SUM(amount), 0) FROM incomes WHERE user_id = '$id') 
                                    - 
                                    (SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE user_id = '$id') 
                                    AS total_balance;
                                    ";
                    $result_balance = $conn->query($sql_balance);
                    if ($result_balance) {
                        $arr_balance = $result_balance->fetch_assoc();
                    } else {
                        echo "Error: " . $sql_balance . "<br>" . $conn->error;
                    }
                    echo "<p id='total-balance'>₹{$arr_balance['total_balance']}</p>";
                    ?>
                </div>
            </div>
            <div class="nav-container">
                <div class="nav-item">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-chart-line not-curr-page"></i>
                    <a href="dashboard.php" class="not-curr-page">Dashboard</a>
                </div>
                <div class="nav-item">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-credit-card not-curr-page"></i>
                    <a href="viewTransactions.php" class="not-curr-page">View Transactions</a>
                </div>
                <div class="nav-item">
                    <p class="currentLine active">|</p>
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                    <a href="incomes.php">Incomes</a>
                </div>
                <div class="nav-item">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-money-bill-transfer not-curr-page"></i>
                    <a href="expenses.php" class="not-curr-page">Expenses</a>
                </div>
            </div>
            <div class="nav-item signout">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <a href="logout.php">Sign Out</a>
            </div>

        </div>
        <div class="right-box">
            <h1>Incomes</h1>
            <div class="income-box">
                <h1>Total Income: &nbsp;</h1>
                <?php
                $sql_income = "SELECT COALESCE(SUM(amount), 0) AS total_income
                            FROM 
                            incomes
                            WHERE user_id = '$id';
                            ";
                $result_income = $conn->query($sql_income);
                if ($result_income) {
                    $arr_income = $result_income->fetch_assoc();
                } else {
                    echo "Error: " . $sql_income . "<br>" . $conn->error;
                }
                echo "<h1 id='balance'>₹{$arr_income['total_income']}</h1>";
                ?>
            </div>
            <div class="partition">
                <div class="form-container">
                    <p class="error" style="display: <?= $emptyError ? 'block' : 'none'; ?>;"><?= $emptyError; ?></p>
                    <form action="incomes.php" method="POST">
                        <input type="text" name="title" placeholder="Income Title" required>
                        <input type="number" name="amount" placeholder="Income Amount" required>
                        <input type="date" name="date" required>
                        <textarea name="reference" id="reference" placeholder="Add A Reference" rows="5" columns="100" required></textarea>
                        <button id="submit-btn" name="submit">+ Add Income</button>
                    </form>
                </div>
                <div class="history-container">
                    <?php
                    $sqlhistory = "SELECT id, title, amount, date, description FROM incomes WHERE user_id = '$id' ORDER BY created_at DESC";

                    $resulthistory = $conn->query($sqlhistory);
                    if ($resulthistory) {
                        if ($resulthistory->num_rows > 0) {
                            while ($income_row = $resulthistory->fetch_assoc()) {
                                echo "
                                <div class='history-box'>
                                        <div class='history-title'>
                                            <i class='fa-solid fa-circle fa-2xs' style='color: #4caf50;'></i>
                                            <h3>{$income_row['title']}</h3>
                                        </div>
                                        <div class='history-info-container'>
                                            <div class='history-info'>
                                                <div class='history-amount'>
                                                    <i class='fa-solid fa-indian-rupee-sign'></i>
                                                    <p>{$income_row['amount']}</p>
                                                </div>
                                                <div class='history-date'>
                                                    <i class='fa-solid fa-calendar'></i>
                                                    <p>{$income_row['date']}</p>
                                                </div>
                                                <div class='history-comment'>
                                                    <i class='fa-solid fa-comment'></i>
                                                    <p>{$income_row['description']}</p>
                                                </div>
                                            </div>
                                            <div class='delete-btn-container'>
                                                <a href='deleteIncome.php?id={$income_row['id']}'><i class='fa-solid fa-trash delete-btn'></i></a>
                                            </div>  
                                        </div>
                                </div>
                                ";
                            }
                        } else {
                            echo "
                            <div class='history-box'>
                                <div class='no-data-found'> 
                                    <p>No income history to show. Add your first income entry to get started!</p>
                                </div>

                            </div>
                            ";
                        }
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>