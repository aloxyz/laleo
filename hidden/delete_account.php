<?php
    require_once("functions.php");
    require_once('../conn.php');

    session_start();

    $account_id = $conn->real_escape_string($_GET['id']);

    if((!(strcmp($_SESSION['role'], 'admin')) || ($_SESSION['id'] == $account_id))){
        $sql = "DELETE FROM accounts WHERE account_ID = '$account_id'";
        $conn->query($sql);
    }

?>