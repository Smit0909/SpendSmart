<?php
session_start();
include "connection.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$id'";
$result = $conn->query($sql);

if ($result) {
    $arrdata = $result->fetch_assoc();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - View Transactions</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/viewTransactions.css">
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
                    <p class="currentLine active">|</p>
                    <i class="fa-solid fa-credit-card"></i>
                    <a href="viewTransactions.php">View Transactions</a>
                </div>
                <div class="nav-item">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-money-bill-trend-up not-curr-page"></i>
                    <a href="incomes.php" class="not-curr-page">Incomes</a>
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
            <h1>Transactions</h1>
            <div class="balance-box">
                <h1>Total Balance: &nbsp;</h1>
                <?php echo "<h1 id='balance'>₹{$arr_balance['total_balance']}</h1>"; ?>
            </div>
            <div class="history-container">
                <?php
                $sqlhistory = "SELECT 
                                        'income' AS type,
                                        id,
                                        user_id,
                                        title,
                                        amount,
                                        description,
                                        date,
                                        created_at
                                        FROM 
                                            incomes
                                        WHERE 
                                            user_id = '$id'

                                        UNION ALL

                                        SELECT 
                                            'expense' AS type,
                                            id,
                                            user_id,
                                            title,
                                            amount,
                                            description,
                                            date,
                                            created_at
                                        FROM 
                                            expenses
                                        WHERE 
                                            user_id = '$id'

                                        ORDER BY 
                                            created_at
                                        DESC;
                                        ";

                $resulthistory = $conn->query($sqlhistory);
                if ($resulthistory) {
                    if ($resulthistory->num_rows > 0) {
                        while ($history_data = $resulthistory->fetch_assoc()) {
                            $color = $history_data['type'] == 'income' ? '#4caf50' : '#ff6347';
                            echo "
                                <div class='history-box'>
                                        <div class='history-title'>
                                            <i class='fa-solid fa-circle fa-2xs' style='color: {$color}'></i>
                                            <h3>{$history_data['title']}</h3>
                                        </div>
                                        <div class='history-info-container'>
                                            <div class='history-info'>
                                                <div class='history-amount'>
                                                    <i class='fa-solid fa-indian-rupee-sign'></i>
                                                    <p>{$history_data['amount']}</p>
                                                </div>
                                                <div class='history-date'>
                                                    <i class='fa-solid fa-calendar'></i>
                                                    <p>{$history_data['date']}</p>
                                                </div>
                                                <div class='history-comment'>
                                                    <i class='fa-solid fa-comment'></i>
                                                    <p>{$history_data['description']}</p>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                                ";
                        }
                    } else {
                        echo "
                            <div class='history-box'>
                                <div class='no-data-found'> 
                                    <p>No history to show. Add your first transaction to get started!</p>
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

</body>

</html>