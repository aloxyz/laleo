<?php
    require_once('../conn.php');
    session_start();

    $chapter_id = $conn->real_escape_string($_POST['chapter_id']);
    $reaction = $conn->real_escape_string($_POST['reaction']);
    $account_ID = $_SESSION['id'];
    
    try{
        $sql = "INSERT INTO chapters_accounts_reactions (reaction, chapter_ID, account_ID) VALUES ('$reaction','$chapter_id','$account_ID')";
        $conn->query($sql);
        echo '1';
    }
    catch(Exception $e){
        $sql = "DELETE FROM chapters_accounts_reactions WHERE reaction='$reaction' AND chapter_ID='$chapter_id' AND account_ID='$account_ID'";
        $conn->query($sql);
        echo '-1';
    }
?>