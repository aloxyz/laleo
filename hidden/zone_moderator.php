<?php   

    function zone_moderator($language){     #says if current user is moderatore of language

        if((strcmp($_SESSION['role'], 'moderator'))){
          return false;
      }
      else{
        global $conn;
        $user_id = $_SESSION['id'];
        $sql = "SELECT moderator_ID, language_name FROM moderators_languages WHERE language_name ='$language' AND moderator_ID = $user_id";
        if($conn->query($sql)->num_rows){
            return true;
        }
        else{
            return false;
        }
      }
    }

?>