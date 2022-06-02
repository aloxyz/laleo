<?php 
    require_once("../conn.php");
    session_start();

    $account_id = $_POST['account_id'];
    $language = $_POST['language'];

    $account_id = $conn->real_escape_string($account_id);
    $language = $conn->real_escape_string($language);

    if($row['author_nickname'] == $account_nickname){
        $sql = "SELECT * FROM accounts_languages WHERE account_ID='$account_id' AND language_name='$language'";
        if($conn->query($sql)->num_rows){
            $sql = "DELETE FROM accounts_languages WHERE account_ID = '$account_id' AND language_name='$language'";
            $conn->query($sql);
        }
    }
?>