<?php
    require_once("conn.php");
    session_start();

    
    if($_POST && ($_SESSION['id'] == $_POST['id'] || $_SESSION['role'] == 'admin')){
        
        $new_name = $conn->real_escape_string($_POST['name']);
        $new_surname = $conn->real_escape_string($_POST['surname']);
        $new_nickname = $conn->real_escape_string($_POST['nickname']);
        $new_country = $conn->real_escape_string($_POST['country']);
        $new_role = $conn->real_escape_string($_POST['role']);
        $new_email = $conn->real_escape_string($_POST['email']);
        $new_password = $conn->real_escape_string($_POST['new_password']);
        $confirmed_password = $conn->real_escape_string($_POST['confirm_password']);
        $id = $conn->real_escape_string($_POST['id']);
        
        $sql = "SELECT role FROM accounts WHERE account_ID ='$id'";
        $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);

        #update name surname and country
        $sql_update = "UPDATE accounts SET name='$new_name', surname='$new_surname', country='$new_country' WHERE account_ID = '$id'; ";

        $error = "";
        #update email
        if ($new_email && !filter_var($new_email, FILTER_VALIDATE_EMAIL))
            $error .= "New email format invalid<br>";
        else{
            $sql_check = "SELECT * FROM accounts WHERE email = '$new_email'";
            if ($result = $conn->query($sql_check))
                if(!($result->num_rows)) 
                    $sql_update .= "UPDATE accounts SET email='$new_email' WHERE account_ID = '$id'; ";
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
                    $sql_update .= "UPDATE accounts SET nickname='$new_nickname' WHERE account_ID = '$id'; ";
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
                        $sql_update .= "UPDATE accounts SET role='$new_role' WHERE account_ID = '$id'; ";
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
            $sql_update .= "UPDATE accounts SET password='$new_password' WHERE account_ID = '$id'; ";        
        }
        
        $conn->multi_query($sql_update);
        while($conn->next_result()){;} #waits for queries to finish
    }

    if(!(isset($_SESSION['id'])))
        header("location: login.php");
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM accounts WHERE account_ID = '$id'";
        
    if($result = $conn->query($sql))
        $row = $result->fetch_array(MYSQLI_ASSOC);
    
    if((!($row))){
            header("location: hidden/user_not_found.html");
    }

?>
<html>
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

<?php echo '<title>'.$row['nickname'].'- Lalèo</title>'?>    
</head>
<body>
<a class="textlogo" href="index.php">Lalèo</a>
<?php echo "<br>.$error"; ?>
<div class="container rounded bg-white mt-5">
        <div class="row">
            <div class="col-md-8">
                <div class="p-3 py-5">
                    <form method="post">
                    <div class="row mt-2">
                    <?php 
                        if(isset($row['name'])){
                            echo'<div class="col-md-6">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" ';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly if current user has no right to edit
                            echo 'value='.$row['name'].'></div>';
                        }
                        if(isset($row['surname'])){
                            echo'<div class="col-md-6">
                            <label for="surname">Surname</label>
                            <input type="text" class="form-control" id="surname" name="surname" placeholder="Surname" ';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly if current user has no right to edit
                            echo'value='.$row['surname'].'></div>';
                        }
                    ?>
                    </div>
                    <div class="row mt-3">
                            <div class="col-md-6">
                            <label for="nickname">Nickname</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" placeholder="Nickname"
                            <?php if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly  if current user has no right to edit ?>
                            value=<?php echo $row['nickname']?> ></div>
                        <?php
                        if(isset($row['country'])){
                            echo'<div class="col-md-6">
                            <label for="country">Country</label>
                            <input type="text" class="form-control" id="country" name="country" placeholder="Country"';
                            if($_SESSION['id']!=$row['account_ID'] && $_SESSION['role']!='admin') echo " readonly "; # readonly  if current user has no right to edit
                            echo'value='.$row['country'].'></div>';
                        }
                    ?>
                    </div>
                    <div class="row mt-3">
                    
                        <div class="col-md-6">
                        <label for="registration_date">Registration Date</label>
                        <input type="text" class="form-control" id="registration_date" readonly 
                        value=<?php echo $row['registration_date']; ?>> 
                        </div>


                        <div class="col-md-6 form-horizontal">
                        <label for="role">Role</label>
                        <?php if($_SESSION['role']!='admin') 
                                echo '<input type="text" class="form-control" id="role" readonly value='.$row['role'].'>';
                            else{
                                echo '<select name="role" id="role" class="form-select">';
                                $sql = 'SELECT role_name FROM roles';
                                if($result = $conn->query($sql))
                                    while ($role = $result->fetch_array(MYSQLI_ASSOC)){
                                        echo $role;
                                        echo'<option value='.$role['role_name'].'>'.$role['role_name'].'</option>';
                                    }
                                echo '</select>';
                            }
                        
                        ?> 
                        </div> 
                    
                    </div>
                    <div class="row mt-3">
                        <?php
                        if($_SESSION['id']==$row['account_ID'] || $_SESSION['role']=='admin'){
                            echo'<div class="col-md-6">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Email"';
                            echo'value='.$row['email'].'></div>';

                            echo'<div class="col-md-6">
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
                        <div class="col-md-6">
                        <label for="new_password">New Password</label>
                        <div class="col-md-6"><input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password"></div>
                        </div>

                        <div class="col-md-6">
                        <label for="confirm_password">Confirm Password</label>
                        <div class="col-md-6"><input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" ></div>
                        </div>
                        ';
                        }
                        ?>
                    </div>
                    <?php
                    if($_SESSION['id']==$row['account_ID'] || $_SESSION['role']=='admin'){
                        echo '<input type="hidden" id="id" name="id" value='.$row['account_ID'].'>';
                        echo '<div class="mt-5 text-right"><input class="btn btn-primary profile-button" type="submit" value="Edit"></div>';
                    }
                    ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>



</html>