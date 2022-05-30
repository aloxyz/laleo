<?php 
    require_once("../conn.php");
    session_start();

    $story_id = $_GET['id'];
    $account_id = $_SESSION['id'];
    $story_id = $conn->real_escape_string($story_id);
    $sql = "SELECT * FROM stories WHERE story_ID ='$story_id'";
    if($conn->query($sql)->num_rows){
        $sql = "SELECT account_ID, story_ID FROM accounts_stories WHERE account_ID ='$account_id' AND story_ID = '$story_id'";
        if(!($conn->query($sql)->num_rows)){
        $sql = "INSERT INTO accounts_stories (account_ID, story_ID) values ('$account_id','$story_id')";
        $conn->query($sql);
        }
    }
?>