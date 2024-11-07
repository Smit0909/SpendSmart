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

//graph implementation

// Fetch income data
$sql_income = "SELECT amount, date FROM incomes WHERE user_id = '$id'";
$result_income = $conn->query($sql_income);

$income_data = [];


if ($result_income->num_rows > 0) {
    while ($row = $result_income->fetch_assoc()) {
        $income_data[$row['date']] = $row['amount'];
    }
}

// Fetch expense data
$sql_expense = "SELECT amount, date FROM expenses WHERE user_id = '$id'";
$result_expense = $conn->query($sql_expense);

$expense_data = [];
if ($result_expense->num_rows > 0) {
    while ($row = $result_expense->fetch_assoc()) {
        $expense_data[$row['date']] = $row['amount'];
    }
}

// Merge dates
$all_dates = array_unique(array_merge(array_keys($income_data), array_keys($expense_data)));
sort($all_dates);

// Map amounts to dates
$income_amounts = [];
$expense_amounts = [];
foreach ($all_dates as $date) {
    $income_amounts[] = $income_data[$date] ?? null;
    $expense_amounts[] = $expense_data[$date] ?? null;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart - Dashboard</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <p class="currentLine active">|</p>
                    <i class="fa-solid fa-chart-line"></i>
                    <a href="dashboard.php">Dashboard</a>
                </div>
                <div class="nav-item">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-credit-card not-curr-page"></i>
                    <a href="viewTransactions.php" class="not-curr-page">View Transactions</a>
                </div>
                <div class="nav-item not-curr-page">
                    <p class="currentLine">|</p>
                    <i class="fa-solid fa-money-bill-trend-up not-curr-page"></i>
                    <a href="incomes.php" class="not-curr-page">Incomes</a>
                </div>
                <div class="nav-item not-curr-page">
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
            <h1>All Transactions</h1>
            <div class="partition">
                <div class="left-part">
                    <div class="graph-box">
                        <canvas id="myChart" width="400" height="200"></canvas>
                        <script>
                            // PHP arrays to JavaScript
                            var allDates = <?php echo json_encode($all_dates); ?>;
                            var incomeAmounts = <?php echo json_encode($income_amounts); ?>;
                            var expenseAmounts = <?php echo json_encode($expense_amounts); ?>;

                            var ctx = document.getElementById('myChart').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: allDates,
                                    datasets: [{
                                        label: 'Income',
                                        data: incomeAmounts,
                                        backgroundColor: 'rgba(76, 175, 80, 0.2)', // Green background
                                        borderColor: 'rgba(76, 175, 80, 1)', // Green border
                                        borderWidth: 1,
                                        cubicInterpolationMode: 'monotone'
                                    }, {
                                        label: 'Expense',
                                        data: expenseAmounts,
                                        backgroundColor: 'rgba(255, 99, 71, 0.2)', // Red background
                                        borderColor: 'rgba(255, 99, 71, 1)', // Red border
                                        borderWidth: 1,
                                        cubicInterpolationMode: 'monotone'
                                    }]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        </script>
                        
                    </div>
                    <div class="info-boxes">
                        <div class="income-box">
                            <h2>Total Income</h2>
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
                            echo "<h1 id='total-income'>₹{$arr_income['total_income']}</h1>";
                            ?>
                        </div>
                        <div class="expense-box">
                            <h2>Total Expense</h2>
                            <?php
                            $sql_expense = "SELECT COALESCE(SUM(amount), 0) AS total_expense
                            FROM 
                            expenses
                            WHERE user_id = '$id';
                            ";
                            $result_expense = $conn->query($sql_expense);
                            if ($result_expense) {
                                $arr_expense = $result_expense->fetch_assoc();
                            } else {
                                echo "Error: " . $sql_expense . "<br>" . $conn->error;
                            }
                            echo "<h1 id='total-expense'>₹{$arr_expense['total_expense']}</h1>";
                            ?>
                        </div>
                        <div class="balance-box">
                            <h2>Total Balance</h2>
                            <?php echo "<h1>₹{$arr_balance['total_balance']}</h1>"; ?>
                        </div>
                    </div>
                </div>
                <div class="right-part">
                    <h2>Recent History</h2>
                    <div class="history-container">
                        <?php

                        $sql_history = "SELECT 
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
                        $result_history = $conn->query($sql_history);
                        if ($result_history) {
                            if ($result_history->num_rows > 0) {
                                $count = 0;
                                while ($history_data = $result_history->fetch_assoc()) {
                                    if ($count < 5) {
                                        $count++;
                                        echo "<div class='history-box {$history_data['type']}'>  
                                            <p>{$history_data['title']}</p>
                                            <p>₹{$history_data['amount']}</p>
                                          </div>";
                                    } else {
                                        break;
                                    }
                                }
                            } else {
                                echo "<div class='history-box'>  
                                            <p>It seems you haven't made any transactions yet.</p>
                                        </div>";
                            }
                        } else {
                            echo "Error: " . $sql_history . "<br>" . $conn->error;
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>