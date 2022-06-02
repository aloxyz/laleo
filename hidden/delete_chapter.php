<?php 
    require_once("../conn.php");
    require_once("functions.php");
    
    session_start();

    $chapter_id = $_GET['id'];
    $account_id = $_SESSION['id'];
    $chapter_id = $conn->real_escape_string($chapter_id);
    
    $sql = "SELECT stories.language AS language, stories.author AS author_nickname FROM stories JOIN chapters ON stories.story_ID = chapters.story_ID WHERE chapter_ID='$chapter_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || !(strcmp($_SESSION['nickname'], $row['author_nickname'])))){
        $sql = "DELETE FROM chapters WHERE chapter_ID = '$chapter_id'";
        $conn->query($sql);
    }

?>