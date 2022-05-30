<?php 
    require_once("../conn.php");
    session_start();

    $story_id = $_GET['id'];
    $account_id = $_SESSION['id'];
    $story_id = $conn->real_escape_string($story_id);
    $sql = "DELETE FROM accounts_stories WHERE account_ID = '$account_id' AND story_ID = '$story_id'";
    $conn->query($sql);
?>