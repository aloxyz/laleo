<?php
    require_once("conn.php");
    require_once("hidden/functions.php");
    session_start();

    if($_POST['languages'] && $_SESSION['role']=='admin'){
        $languages = $_POST['languages'];
        $account_id = $conn->real_escape_string($_GET['id']);
        if(!(verify_languages_errors($languages))){
            $sql = "SELECT language_name FROM moderators_languages WHERE moderator_ID='$account_id'";
            if($result = $conn->query($sql)){
                while($account_languages[] = $result->fetch_array(MYSQLI_ASSOC));
            }
            add_moderator_languages($account_id, $languages, $account_languages);
            
        }
    }
    else if($_POST && ($_SESSION['id'] == $_POST['id'] || $_SESSION['role'] == 'admin')){
        
        $new_name = $conn->real_escape_string($_POST['name']);
        $new_surname = $conn->real_escape_string($_POST['surname']);
        $new_nickname = $conn->real_escape_string($_POST['nickname']);
        $new_country = $conn->real_escape_string($_POST['country']);
        $new_role = $conn->real_escape_string($_POST['role']);
        $new_email = $conn->real_escape_string($_POST['email']);
        $new_password = $conn->real_escape_string($_POST['new_password']);
        $confirmed_password = $conn->real_escape_string($_POST['confirm_password']);
        $account_id = $conn->real_escape_string($_POST['id']);
        
        $sql = "SELECT role FROM accounts WHERE account_ID ='$account_id'";
        $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

        #update name surname and country
        $sql_update = "UPDATE accounts SET name='$new_name', surname='$new_surname', country='$new_country' WHERE account_ID = '$account_id'; ";

        $error = "";
        #update email
        if ($new_email && !filter_var($new_email, FILTER_VALIDATE_EMAIL))
            $error .= "New email format invalid<br>";
        else{
            $sql_check = "SELECT * FROM accounts WHERE email = '$new_email'";
            if ($result = $conn->query($sql_check))
                if(!($result->num_rows)) 
                    $sql_update .= "UPDATE accounts SET email='$new_email' WHERE account_ID = '$account_id'; ";
                else
                    $error .= "There is already an account associated with this mail<br>";
            }

        #update nickname
        if (!($new_nickname) || !(is_string($new_nickname)) || $new_nickname == '')
            $error .= "New nickanme invalid<br>";
        else{
            $sql_check = "SELECT * FROM accounts WHERE nickname = '$new_nickname'";
            if ($result = $conn->query($sql_check))
                if(!($result->num_rows)) 
                    $sql_update .= "UPDATE accounts SET nickname='$new_nickname' WHERE account_ID = '$account_id'; ";
                else
                $error .= "There is already an account associated with this nickname<br>";
        }
        
        #update role
        if ($_SESSION['role'] == 'admin')
            if($row['role']=='admin')
            $error .= "You can't change an admin role<br>";
            else if($new_role == "admin")
                $error .= "You can't name another admin<br>";
            else{
                $sql_check = "SELECT role_name FROM roles WHERE role_name='$new_role'";
                if ($result = $conn->query($sql_check))
                    if(($result->num_rows)){
                        $sql_update .= "UPDATE accounts SET role='$new_role' WHERE account_ID = '$account_id'; ";
                    }
                    else
                    $error .= "That role doesn't exist<br>";
            }
        
        #update password
        if (!($confirmed_password === $new_password))
            $error .= "Confirm password wrong<br>";
        else if(!($new_password) || !(is_string($new_password)) || $new_password == '')
            $error .= "New password can't be empty <br>";
        else{
            $new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update .= "UPDATE accounts SET password='$new_password' WHERE account_ID = '$account_id'; ";        
        }
        
        $conn->multi_query($sql_update);
        while($conn->next_result()){;} #waits for queries to finish
    }

    if(!(isset($_SESSION['id'])))
        header("location: login.php");
    $account_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM accounts WHERE account_ID = '$account_id'";
        
    if($result = $conn->query($sql))
        $row = $result->fetch_array(MYSQLI_ASSOC);
    
    if((!($row))){
            header("location: hidden/user_not_found.html");
    }

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>    
<script>
$(document).ready(function(){
  $("#follow").on("click", function(){

    $("#unfollow").attr('hidden', false);
    $("#follow").attr('hidden', true);

    $.get("hidden/follow_account.php?id=<?php echo $account_id ?>");
  });

  $("#unfollow").on("click", function(){

    $("#follow").attr('hidden', false);
    $("#unfollow").attr('hidden', true);   

    $.get("hidden/unfollow_account.php?id=<?php echo $account_id ?>");
  });

  $(".language_moderator_tag").on("click", function(){
    if(<?php echo'"'.$_SESSION['role'].'"';?>== 'admin'){
        console.log('eee');
        $(this).attr('hidden', true);
        $.post("hidden/remove_moderator_language.php",
        {
        moderator_id:<?php echo $account_id;?>,
        language:$(this).attr('value')
        }
        )
    }
    });

});
</script>

<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">    
    <?php echo '<title>'.$row['nickname'].'- Lalèo</title>'?>    

</head>
<body>
<a class="textlogo" href="index.php">Lalèo</a>
<div class="container rounded bg-white mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="p-3 py-5">
                    <div class="row mt-2">
                        <div class="col-md-4">
                            <a class="btn btn-primary profile-button" href="followers.php?id=<?php echo $account_id;?>">Followers</a>
                        </div>
                        <div class="col-md-4">
                        <a class="btn btn-primary profile-button" href="followed_users.php?id=<?php echo $account_id;?>">Followeds</a>
                        </div>

                        <?php 
                            $sql = "SELECT * FROM followers_followeds where follower_ID = '$_SESSION[id]' AND followed_ID = '$account_id'";
                            if($result = $conn->query($sql)){
                            $tmp_row = $result->fetch_array(MYSQLI_ASSOC);
                            if($tmp_row){
                                echo '<button class="btn btn-primary profile-button unfollow" id="unfollow">Unfollow</button>';
                                echo '<button class="btn btn-primary profile-button follow" id="follow" hidden>Follow</button>';
                            }
                        else{
                            echo '<button class="btn btn-primary profile-button unfollow" id="unfollow" hidden>Unfollow</button>';
                            echo '<button class="btn btn-primary profile-button follow" id="follow">Follow</button>';
                            }
                        }
                        ?>
                    
                    </div>
                    <form method="post">
                    <div class="row mt-2">
                    <?php 
                        if(isset($row['name'])){
                            echo'<div class="col-md-4">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" ';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly if current user has no right to edit
                            echo 'value='.$row['name'].'></div>';
                        }
                        if(isset($row['surname'])){
                            echo'<div class="col-md-4">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" id="surname" name="surname" placeholder="Surname" ';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly if current user has no right to edit
                            echo'value='.$row['surname'].'></div>';
                        }
                    ?>
                    </div>
                    <div class="row mt-3">
                            <div class="col-md-4">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname"
                            <?php if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly  if current user has no right to edit ?>
                            value=<?php echo $row['nickname']?> ></div>
                        <?php
                        if(isset($row['country'])){
                            echo'<div class="col-md-4">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" placeholder="Country" ';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly  if current user has no right to edit
                            echo'value='.$row['country'].'></div>';
                        }
                    ?>
                        <div class="col-md-4">
                            <a class="btn btn-primary profile-button" href="created_stories.php?id=<?php echo $account_id;?>">Created Stories</a>
                        </div>
                    </div>
                    <div class="row mt-3">
                    
                        <div class="col-md-4">
                        <label for="registration_date">Registration Date</label>
                        <input type="text" class="form-control" id="registration_date" readonly 
                        value=<?php echo $row['registration_date']; ?>> 
                        </div>


                        <div class="col-md-4 form-horizontal">
                        <label for="role">Role</label>
                        <?php if($_SESSION['role']!='admin' || $row['role']=='admin') 
                                echo '<input type="text" class="form-control" id="role" readonly value='.$row['role'].'>';
                            else{
                                echo '<select name="role" id="role" class="form-select">';
                                $sql = 'SELECT role_name FROM roles';
                                if($result = $conn->query($sql))
                                    while ($role = $result->fetch_array(MYSQLI_ASSOC)){
                                        if($role['role_name']==$row['role']){
                                        echo'<option value='.$role['role_name'].' selected>'.$role['role_name'].'</option>';
                                        }
                                        else{
                                        echo'<option value='.$role['role_name'].'>'.$role['role_name'].'</option>';
                                        }
                                    }
                                echo '</select>';
                            }
                        
                        ?> 
                        </div> 
                            
                        <div class="col-md-4">
                            <a class="btn btn-primary profile-button" href="followed_stories.php?id=<?php echo $account_id;?>">Followed Stories</a>
                        </div>

                    </div>
                    <div class="row mt-3">
                        <?php
                        if($_SESSION['id']==$row['account_ID'] || $_SESSION['role']=='admin'){
                            echo'<div class="col-md-5">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email" ';
                            echo'value='.$row['email'].'></div>';

                            echo'<div class="col-md-3">
                            <label for="birthdate">Birthdate</label>
                            <input type="date" class="form-control" id="birthdate" placeholder="Birthdate" readonly ';
                            echo'value='.$row['birthdate'].'></div>';
                        }
                        ?>
                    </div>
                    <div class="row mt-3">
                        <?php
                        if($_SESSION['id']==$row['account_ID'] || $_SESSION['role']=='admin'){
                        echo '
                        <div class="col-md-4">
                        <label for="new_password">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password"></div>
                        

                        <div class="col-md-4">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" ></div>
                        
                        ';
                        }
                        ?>
                    </div>
                    <?php
                    if($_SESSION['id']==$row['account_ID'] || $_SESSION['role']=='admin'){
                        echo '<input type="hidden" id="id" name="id" value='.$row['account_ID'].'>';
                        echo '<div class="row mt-3">
                                <div class="col-md-4">
                                    <a class="btn btn-primary profile-button" href="homepage-php" id="delete">Delete</a>    
                                </div>
                                <div class="col-md-4">
                                    <input class="btn btn-primary profile-button" type="submit" value="Edit">
                                </div>';
                    }
                    if($_SESSION['id']==$row['account_ID']){
                        echo    '<div class="col-md-4">
                                    <a class="btn btn-primary profile-button" href="edit_genres_languages.php">Edit Genres and Languages</a>
                                </div>
                            </div>';                        
                    }
                    ?>
                    </form>
                </div></div>
                    <div class="row mt-3"> Followed genres:
        <?php
                $sql = "SELECT genre_name FROM accounts_genres WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                        while($genre = $result->fetch_array(MYSQLI_ASSOC)){
                            if($genre['genre_name']!='')
                                echo '<div class="col-sm-1 genre_tag" value='.$genre['genre_name'] .'>
                            '.$genre['genre_name'] .'
                                </div>';    
                        }        
                }
        ?>
                </div>

                <div class="row mt-3 pt-4"> Languages:
        <?php
                $sql = "SELECT language_name FROM accounts_languages WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                        while($language = $result->fetch_array(MYSQLI_ASSOC)){
                            if($language['language_name']!='')
                                echo '<div class="col-sm-1 language_tag" value='.$language['language_name'] .'>
                            '.$language['language_name'] .'
                                </div>';    
                        }        
                }
        ?>
                </div>
        <?php
            if($row['role']=='moderator'){
                echo '<div class="row mt-3 pt-4"> Moderator of:';
                $sql = "SELECT language_name FROM moderators_languages WHERE moderator_ID='$account_id'";
                if($result = $conn->query($sql)){
                    while($language = $result->fetch_array(MYSQLI_ASSOC)){
                        if($language['language_name']!='')
                            echo '<div class="col-md-2 language_moderator_tag" value='.$language['language_name'] .'>
                    '.$language['language_name'].'</div>
                            ';                            
                    }        
                }
                echo '</div>';
            }
        ?>
        <?php
        if($_SESSION['role']=='admin' && $row['role']=='moderator'){
            echo '<div class="row mt-3 pt-4">Add new languages to moderate:';
            echo '<form method="post">
                  <select name="languages[]" id="language" class="form-select" multiple="multiple" autocomplete>';
                $sql = 'SELECT language_name FROM languages';
                if($result = $conn->query($sql))
                    while ($new_language = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$new_language['language_name'].'>'.$new_language['language_name'].'</option>';
                    }
            echo' 
                  </select>
                  <div class="row"><input class="button" type="submit" value="Add Languages to moderate"></div>
            </form>
            </div>';
        }
        ?>


                </div>  
    </div>
</body>



</html>