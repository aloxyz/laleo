<?php 
    require_once("../conn.php");
    session_start();

    $story_id = $_POST['story_id'];
    $genre = $_POST['genre'];
    $account_nickname = $_SESSION['nickname'];

    $story_id = $conn->real_escape_string($story_id);
    $genre = $conn->real_escape_string($genre);

    $sql = "SELECT 
            stories.author AS author_nickname
            stories.language AS language
        FROM stories
        JOIN accounts
        ON stories.author = accounts.nickname
        WHERE stories.story_ID = '$story_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
    if($row['author_nickname'] == $account_nickname || zone_moderator($row['language'] || $_SESSION['role'] == "admin")){
        $sql = "SELECT * FROM genres_stories WHERE story_ID='$story_id' AND genre_name='$genre'";
        if($conn->query($sql)->num_rows){
            $sql = "DELETE FROM genres_stories WHERE story_ID = '$story_id' AND genre_name= '$genre'";
            $conn->query($sql);
        }
    }
?>