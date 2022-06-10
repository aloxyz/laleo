<?php
    require_once("conn.php");
    session_start();

    $account_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM accounts WHERE account_ID='$account_id'";
    if($result = $conn->query($sql))
        if(!($result->num_rows)){
            header("Location: hidden/user_not_found.php");
        }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $(".unfollow").on("click", function(){
        
        var story_id = $(this).attr('id');
        $('.row.story.'+story_id).attr('hidden', true);
        $.get("hidden/unfollow_story.php?id="+story_id);
});
});
</script>

<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <title>Followed Stories - Lalèo</title>
</head>
<body>
    <div class="container">
    <?php
         $sql = "SELECT title, stories.story_ID FROM stories WHERE stories.author_ID='$account_id'";

         if($result = $conn->query($sql)){
                 while($story = $result->fetch_array(MYSQLI_ASSOC)){
                     if($story['title']!='')
                     echo'<div class="row pt-1 story '.$story['story_ID'].'">
                     <div class="col-sm-3">
                         <a href="story.php?id='.$story['story_ID'].'">'.$story['title'].'</a> 
                     </div>';
                    if($_SESSION['id'] == $account_id){
                    echo '
                     <div class="col-sm-6">
                        <button class="unfollow btn btn-primary profile-button" id="'.$story['story_ID'].'">Unfollow</button>
                    </div>';
                   }          
                   echo '</div>';
           }
        }
    ?>
    </div>
</body>
</html>