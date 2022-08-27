<?php
    require_once('../conn.php');
    session_start();

    $thought_id = $conn->real_escape_string($_POST['thought_id']);
    $reaction = $conn->real_escape_string($_POST['reaction']);
    $account_ID = $_SESSION['id'];
    
    $sql = "SELECT * FROM thoughts_accounts_reactions WHERE reaction='$reaction' AND thought_ID='$thought_id' AND account_ID='$account_ID'";

    if(!($conn->query($sql)->num_rows)){
        $sql = "INSERT INTO thoughts_accounts_reactions (reaction, thought_ID, account_ID) VALUES ('$reaction','$thought_id','$account_ID')";
        $conn->query($sql);
        echo '1';
    }
    else{
    $sql = "DELETE FROM thoughts_accounts_reactions WHERE reaction='$reaction' AND thought_ID='$thought_id' AND account_ID='$account_ID'";
        $conn->query($sql);
        echo '-1';
    }
?>