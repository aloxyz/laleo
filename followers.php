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

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <title>Followers - Lalèo</title>
</head>
<body>
    <?php
         $sql = "SELECT accounts.account_ID, accounts.nickname FROM accounts JOIN followers_followeds ON  followers_followeds.follower_ID=accounts.account_ID 
                 WHERE followers_followeds.followed_ID='$account_id'";

         if($result = $conn->query($sql)){
                 while($account = $result->fetch_array(MYSQLI_ASSOC)){
                     if($account['nickname']!='')
                         echo '<div class="row account_tag"><div class="col-sm-4"><a href="profile.php?id='.$account['account_ID'].'">
                     '.$account['nickname'] .'
                         </a></div></div>';    
                 }        
         }
    ?>
</body>
</html>