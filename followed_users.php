<?php
    require_once("conn.php");
    session_start();

    $account_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM accounts WHERE account_ID='$account_id'";
    if($result = $conn->query($sql))
            if($result->num_rows){
                header("hidden/user_not_found");
            }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $(".unfollow").on("click", function(){
        
        var account_id = $(this).attr('id');
        $('.row.account.'+account_id).attr('hidden', true);
        $.get("hidden/unfollow_account.php?id="+account_id);
});
});
</script>

<html lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <title>Followed Users - Lalèo</title>
</head>
<body>
    <div class="container">
    <?php
         $sql = "SELECT accounts.account_ID, accounts.nickname FROM accounts JOIN followers_followeds ON  followers_followeds.followed_ID=accounts.account_ID 
                 WHERE followers_followeds.follower_ID='$account_id'";

         if($result = $conn->query($sql)){
                 while($account = $result->fetch_array(MYSQLI_ASSOC)){
                     if($account['nickname']!='')
                     echo'<div class="row pt-1 account '.$account['account_ID'].'">
                     <div class="col-sm-3">
                         <a href="account.php?id='.$account['account_ID'].'">'.$account['nickname'].'</a> 
                     </div>';
                    if($_SESSION['id'] == $account_id){
                echo'
                        <div class="col-sm-6">
                        <button class="unfollow btn btn-primary profile-button" id="'.$account['account_ID'].'">Unfollow</button>
                 </div>';
                   }
                   echo '</div>';
                }          
         }
    ?>
    </div>
</body>
</html>