<?php 
    require_once("../conn.php");
    require_once("functions.php");
    session_start();

    $story_id = $_POST['story_id'];
    $genre = $_POST['genre'];
    $account_id = $_SESSION['id'];

    $story_id = $conn->real_escape_string($story_id);
    $genre = $conn->real_escape_string($genre);

    $sql = "SELECT 
            stories.author_ID AS author_ID,
            stories.language AS language
        FROM stories
        JOIN accounts
        ON stories.author_ID = accounts.account_ID
        WHERE stories.story_ID = '$story_id'";

    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
    if($row['author_ID'] == $account_id || zone_moderator($row['language'] || $_SESSION['role'] == "admin")){
        $sql = "SELECT * FROM genres_stories WHERE story_ID='$story_id' AND genre_name='$genre'";
        if($conn->query($sql)->num_rows){
            $sql = "DELETE FROM genres_stories WHERE story_ID = '$story_id' AND genre_name= '$genre'";
            $conn->query($sql);
        }
    }
?>