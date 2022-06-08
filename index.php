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
            <input id="navsearch" type="text" placeholder="Search on Lalèo">
        <div class="flex-row">
            <div class="navlinks">
                <?php if (empty($_SESSION['role'])){
                echo '<a class="link" href="login.php">Login</a>';
                echo '<a class="link" href="signup.php">Signup</a>';
                }
                else{
                    echo '
                    <div>
                        <a class="button" href="create_story.php">New story</a>
                        <a class="button" href="create_chapter.php">New chapter</a>
                    </div>
                    ';
                    echo '<a class="link" href="profile.php?id='.$_SESSION['id'].'">Edit Profile</a>';
                    echo '<a class="link" href="logout.php">Logout</a>';
                }
                ?>
            </div>
        </div>
    </div>
        <div class="feed-header">

        </div>
    
    <div class="feed">
        <div class="box chapter">
            <div class="post-head">
                    <p class="post-title">Chapter title </p>
            <p class="italic">from <a>Story</a> by <a>Author</a></p>
            </div>
            
            <div class="post-head">
                <p class="italic">01/02/2003</p>
                <p class="react">16 ❤️</p>
            </div>
        </div>

        
        <div class="box story">
            <div class="post-head">
                    <p class="post-title">Story title </p>
            <p class="italic">written in Language by <a>Author</a></p>
            </div>
                <img src="https://nerdarchy.com/wp-content/uploads/2018/08/tavern-1024x551.jpg">
            <div class="post-head">
                <p class="italic">01/02/2003</p>
                <p class="react">87 ❤️</p>
            </div>
        </div>
    </div>
</body>
</html>