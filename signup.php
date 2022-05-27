<?php 
    require_once('conn.php'); 
    session_start();
    if($_POST){         
        $error = "";

        if (!$_POST['email'])
            $error .= "Email required<br>";
        if (!$_POST['nickname'])
            $error .= "Nickname required<br>";
        if (!$_POST['password'])
            $error .= "Password required<br>";
        if ($_POST['email'] && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
            $error .= "Email format invalid<br>";
        if (!$_POST['confirmed_password'])
            $error .= "You must confirm password<br>";
        if (!($_POST['confirmed_password'] === $_POST['password']))
            $error .= "Confirm password wrong<br>";
        
        $sql = "SELECT * FROM Account WHERE email = '"
                .$_POST['email']." '
            ";
    
        if ($result = $conn->query($sql) && $result->num_rows) {
                $error .= "There is already an account associated with this mail";
            }
        
        $sql = "SELECT * FROM Account WHERE nickname = '"
            .$_POST['nickname']." '
        ";
        if ($result = $conn->query($sql) && $result->num_rows) {
            $error .= "There is already an account associated with this nickname";
        }

        $today = date("Y-m-d");
        $diff = date_diff(date_create($today), date_create($_POST["birthdate"]));        

        if ($diff->format('%y') < 12) {
            $error .= "You must be at least 12 years old";
        }
        else if($diff->format('%y') > 120){
            $error .= "You should rest at that age!";
        }

        if ($error)
            $error = "Some fields are invalid:<br> $error";
        else{
            $email = $conn->real_escape_string($_POST['email']);
            $nickname = $conn->real_escape_string($_POST['nickname']);
            $password = $conn->real_escape_string($_POST['password']);
            $password = password_hash($password, PASSWORD_DEFAULT);

            if(!empty($_POST['name']))
            $name = $conn->real_escape_string($_POST['name']);
            else
            $name = "NULL";

            if(!empty($_POST['surname']))
            $surname = $conn->real_escape_string($_POST['surname']);
            else
            $surname = "NULL";

            if(!empty($_POST['birthdate']))
            $birthdate = $conn->real_escape_string($_POST['birthdate']);
            else
            $birthdate = "NULL";

            if(!empty($_POST['country']))
            $country = $conn->real_escape_string($_POST['country']);
            else
            $country = "NULL";

            $sql = "INSERT INTO Account (nickname, email, password, nome, cognome, paese, data_nascita, data_registrazione, ruolo) 
                    VALUES ('$nickname', '$email', '$password', '$name', '$surname', '$country', '$birthdate', '$today', 'Utente')";

            if ($result = $conn->query($sql)){
                header("location: login.php");
            }
            else{
                $error .= "Something went wrong";
            }

        }
    }
    $conn->close();  
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Sign up - Lalèo</title>
</head>
<body>
    <a class="textlogo" href="index.php">Lalèo</a>
    <?php echo $error; ?>
    <form class="formbox" method="post">
        <p class="title">Account registration</p>
        <a class="link" href="login.php">Click here to log in an existing account</a>
        <div>
            <p>*: optional</p>    
        </div>
        <ul>
            <li>
                <p>Email</p>
                <input class="form" type="text" name="email" placeholder="foo.bar@example.com" value=<?php echo $_POST['email']; ?> >
            </li>
            
            <li>
                <input class="form" type="text" name="nickname" placeholder="Nickname" value=<?php echo $_POST['nickname']; ?>>
            </li>
            
            <li>
                <p>Create a strong password</p>
                <input class="form" type="password" name="password" placeholder="Password">
            </li>
            
            <li>
                <p>Retype your password</p>
                <input class="form" type="password" name="confirmed_password" placeholder="Retype your password">
            </li>
            
            <li>
                <p>Date of birth</p>
                <input class="form" type="date" name="birthdate" value=<?php echo $_POST['birthdate']; ?>>
            </li>

            <li>
                <p>Name*</p>
                <input class="form" type="text" name="name" placeholder="John" value=<?php echo $_POST['name']; ?>>
            </li>

            <li>
                <p>Surname*</p>
                <input class="form" type="text" name="surname" placeholder="Smith" value=<?php echo $_POST['surname']; ?>>
            </li>

            <li>
                <p>Country*</p>
                <input class="form" type="text" name="country" placeholder="Country" value=<?php echo $_POST['country']; ?>>
            </li>

            <li>
                <input class="button" type="submit" value="Create an account">
            </li>
        </ul>

    </form>
</body>
</html>