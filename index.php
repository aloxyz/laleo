<?php
    require_once('conn.php');
    session_start();
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Front page - Lalèo</title>
</head>
<body>
    <div class="navbar">
        <a class="textlogo" href="#">Lalèo</p>
        <form method="post">
            <input class="navsearch" type="text" placeholder="Search on Lalèo">
        </form>
        <?php if (empty($_SESSION['role'])){
        echo '<a class="link" href="login.php">Login</a>';
        echo '<a class="link" href="signup.php">Signup</a>';
        }
        else{
            echo '<a class="link" href="profile.php?id='.$_SESSION['id'].'">Edit Profile</a>';
            echo '<a class="link" href="logout.php">Logout</a>';
        }
        ?>
    </div>

    <div class="feed">
        <div class="box">
            <div class="post-head">
                <img class="post-profile-picture" src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">
                <a class="post-author">Mario Rossi</a>
            </div>
            <p class="post-title">Post title</p>
            <p class="post-content">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, 
            sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
            Cras adipiscing enim eu turpis egestas pretium aenean. 
            Diam sit amet nisl suscipit adipiscing bibendum. 
            Suspendisse potenti nullam ac tortor vitae purus faucibus.
            Rutrum quisque non tellus orci ac auctor augue. 
            Enim eu turpis egestas pretium aenean pharetra magna ac. 
            Aliquet sagittis id consectetur purus ut faucibus. 
            Quam vulputate dignissim suspendisse in est ante in nibh mauris. 
            Ante metus dictum at tempor commodo ullamcorper a lacus vestibulum. 
            Suspendisse interdum consectetur ...<a href="chapter.php?id=1"> Read full post</a>
            </p>
            <a class="thoughts">12 Thoughts</a>
            <div class="reactions">
                <p class="react">5 ❤️</p>
                <p class="react">3 🎉</p>
                <p class="react">2 🔥</p>
            </div>
        </div>
    </div>
</body>
</html>