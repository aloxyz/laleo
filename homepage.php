<?php
    require_once('conn.php');
    require('hidden/functions.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Lalèo - homepage</title>
</head>
<body>

    <?php print_navbar() ?>
    <div class="container">
        <div id="homepage-logo-container">
            <p class="textlogo" id="homepage-logo">Welcome to<br>Lalèo</p>
            <p id="homepage-desc">Create and publish your stories while connecting with people</p>

            <a class="button" href="signup.php">Get started</a>
            
        </div>
    </div>
        
    </div>
</body>
</html>