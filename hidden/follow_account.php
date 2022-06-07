<?php 
    require_once("../conn.php");
    session_start();

    $to_follow_account_id = $_GET['id'];
    $current_account_id = $_SESSION['id'];
    $to_follow_account_id = $conn->real_escape_string($to_follow_account_id);
    $sql = "SELECT * FROM accounts WHERE account_ID ='$to_follow_account_id'"; 
    if($conn->query($sql)->num_rows){ #checks if account to be followed exists
        $sql = "SELECT * FROM followers_followeds WHERE follower_ID ='$current_account_id' AND followed_ID = '$to_follow_account_id'";
        if(!($conn->query($sql)->num_rows)){ #checks if you don't already follow that account
        $sql = "INSERT INTO followers_followeds (follower_ID, followed_ID) values ('$current_account_id','$to_follow_account_id')";
        $conn->query($sql);
        }
    }
?>