<?php 
    require_once("../conn.php");
    session_start();

    $account_id = $_GET['id'];
    $current_account_id = $_SESSION['id'];
    $account_id = $conn->real_escape_string($account_id);
    $sql = "DELETE FROM followers_followeds WHERE follower_ID = '$current_account_id' AND followed_ID = '$account_id'";
    $conn->query($sql);
?>