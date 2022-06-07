<?php
    require_once("functions.php");
    require_once('../conn.php');

    session_start();

    $story_id = $conn->real_escape_string($_POST['story_id']);
    $bool = $conn->real_escape_string($_POST['bool']);

    $sql = "SELECT language, author FROM stories WHERE stories.story_ID = '$story_id'";
    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

    if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))){
        $sql = "UPDATE stories SET hidden_flag = (?) WHERE story_ID=".$story_id;
        $query = $conn->prepare($sql);
        $query -> bind_param('i', $bool);
        $query->execute();
        
        $sql = "UPDATE chapters SET hidden_flag = (?) WHERE chapters.story_ID=".$story_id;
        $query = $conn->prepare($sql);
        $query -> bind_param('i', $bool);
        $query->execute();
    
    }

?>