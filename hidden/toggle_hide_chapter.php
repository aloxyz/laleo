<?php
    require_once("functions.php");
    require_once('../conn.php');

    session_start();

    $chapter_id = $conn->real_escape_string($_POST['chapter_id']);
    $bool = $conn->real_escape_string($_POST['bool']);

    $sql = "SELECT stories.language AS language FROM stories JOIN chapters ON stories.story_ID = chapters.story_ID WHERE chapter_ID='$chapter_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);


    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))){
        $sql = "UPDATE chapters SET hidden_flag = (?) WHERE chapter_ID=".$chapter_id;
        $query = $conn->prepare($sql);
        $query -> bind_param('i', $bool);
        $query->execute();

        $sql = "UPDATE thoughts SET hidden_flag = (?) WHERE thoughts.chapter_ID=".$chapter_id;
        $query = $conn->prepare($sql);
        $query -> bind_param('i', $bool);
        $query->execute();
    }

?>