<?php 
    require_once("../conn.php");
    require_once("functions.php");
    
    session_start();

    $story_id = $_GET['id'];
    $account_id = $_SESSION['id'];
    $story_id = $conn->real_escape_string($story_id);
    
    $sql = "SELECT language, author_ID FROM stories WHERE stories.story_ID = '$story_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || !(strcmp($_SESSION['id'], $row['author_ID'])))){
        $sql = "DELETE FROM stories WHERE story_ID = '$story_id'";
        $conn->query($sql);
    }

?>