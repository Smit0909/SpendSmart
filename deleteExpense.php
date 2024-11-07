<?php
session_start();
include "connection.php";

if ($_GET['id']) {
    $idNo = $_GET['id'];
    $sql = "DELETE FROM expenses WHERE id='$idNo'";

    $result = $conn->query($sql);

    if ($result) {
        header("Location: expenses.php");
    } else {
        echo "Query Error: " . $conn->error;
    }

    $conn->close();
}
