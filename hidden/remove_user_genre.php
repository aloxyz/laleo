<?php 
    require_once("../conn.php");
    session_start();

    $account_id = $_POST['account_id'];
    $genre = $_POST['genre'];

    $account_id = $conn->real_escape_string($account_id);
    $genre = $conn->real_escape_string($genre);

    if($row['author_nickname'] == $account_nickname){
        $sql = "SELECT * FROM accounts_genres WHERE account_ID='$account_id' AND genre_name='$genre'";
        if($conn->query($sql)->num_rows){
            $sql = "DELETE FROM accounts_genres WHERE account_ID = '$account_id' AND genre_name='$genre'";
            $conn->query($sql);
        }
    }
?>