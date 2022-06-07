<?php 
    require_once("../conn.php");
    session_start();

    $moderator_id = $_POST['moderator_id'];
    $language = $_POST['language'];

    $moderator_id = $conn->real_escape_string($moderator_id);
    $language = $conn->real_escape_string($language);

    if($_SESSION['role']=='admin'){
        $sql = "SELECT * FROM moderators_languages WHERE moderator_ID='$moderator_id' AND language_name='$language'";
        if($conn->query($sql)->num_rows){
            $sql = "DELETE FROM moderators_languages WHERE moderator_ID = '$moderator_id' AND language_name='$language'";
            $conn->query($sql);
        }
    }
?>