<?php
    require_once("functions.php");
    require_once('../conn.php');

    session_start();

    $thought_id = $conn->real_escape_string($_POST['thought_id']);
    $bool = $conn->real_escape_string($_POST['bool']);

    $sql = "SELECT stories.language AS language FROM stories 
            JOIN chapters ON stories.story_ID = chapters.story_ID 
            JOIN thoughts ON thoughts.chapter_ID = chapters.chapter_ID 
            WHERE thought_ID='$thought_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))){
        $sql = "UPDATE thoughts SET hidden_flag = (?) WHERE thought_ID=".$thought_id;
        $query = $conn->prepare($sql);
        $query -> bind_param('i', $bool);
        $query->execute();
    }

?>