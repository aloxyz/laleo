<?php
    require_once("functions.php");
    require_once('../conn.php');

    session_start();

    $thought_id = $conn->real_escape_string($_GET['id']);

    $sql = "SELECT stories.language AS language FROM stories 
            JOIN chapters ON stories.story_ID = chapters.story_ID 
            JOIN thoughts ON thoughts.chapter_ID = chapters.chapter_ID 
            WHERE thought_ID='$thought_id'";
            
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || !(strcmp($_SESSION['id'], $row['author_ID'])))){
        $sql = "DELETE FROM thoughts WHERE thought_ID = '$thought_id'";
        $conn->query($sql);
    }

?>