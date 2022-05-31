<?php 
    require_once("../conn.php");
    require_once("zone_moderator.php");
    session_start();

    $chapter_id = $_GET['id'];
    $account_id = $_SESSION['id'];
    $chapter_id = $conn->real_escape_string($chapter_id);
    
    $sql = "SELECT language, author FROM stories WHERE stories.chapter_ID = '$chapter_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || !(strcmp($_SESSION['nickname'], $row['author_nickname'])))){
        $sql = "DELETE FROM stories WHERE chapter_ID = '$chapter_id'";
        $conn->query($sql);
    }

?>