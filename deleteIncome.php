<?php
session_start();
include "connection.php";

if ($_GET['id']) {
    $idNo = $_GET['id'];
    $sql = "DELETE FROM incomes WHERE id='$idNo'";

    $result = $conn->query($sql);

    if ($result) {
        header("Location: incomes.php");
    } else {
        echo "Query Error: " . $conn->error;
    }

    $conn->close();
}
