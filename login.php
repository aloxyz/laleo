<?php
    require_once('conn.php');
    session_start();

    if($_POST){
        $errore = "";
        $nickname = $conn->real_escape_string($_POST['nickname']);
        $password = $conn->real_escape_string($_POST['password']);

        $sql = "SELECT * FROM accounts WHERE nickname = '$nickname'";
        if($result = $conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if (password_verify($password, $row['password'])) {
    
                $_SESSION['id'] = $row['ID'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['nickname'] = $row['nickname'];
                
                header("location: index.php");
    
    
            } else
                $error = "email or password provided are not correct";
    
        } else
            $error = "email or password provided are not correct";
    }
    $conn->close();  

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Login - Lalèo</title>
</head>
<body>
    <a class="textlogo" href="index.php">Lalèo</a>

    <?php echo $error; ?>
    <form class="formbox" method="post">
        <p class="title">Login</p>
        <input class="form" type="text" name="nickname" placeholder="Nickname" value=<?php echo $_POST['nickname']; ?>>
        <input class="form" type="password" name="password" placeholder="Password" value=<?php echo $_POST['password']; ?>>
        
        <input class="button" type="submit" value="Login">
    </form>

    <a class="link" href="signup.php">Sign up for an account</a>
</body>
</html>