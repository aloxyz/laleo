<?php
    require_once('conn.php');
    require('hidden/functions.php');
    session_start();


    #Branch to take if a custom search was made
    if(isset($_GET['keyword'])){
        $keyword = $conn->real_escape_string($_GET['keyword']);
        $sql_chapters = "SELECT chapter_ID,
                                chapters.title, 
                                stories.title AS story_title, 
                                accounts.nickname AS author_nickname,
                                accounts.account_ID AS author_ID,
                                chapters.pubblication_time,
                                chapters.total_votes,
                                chapters.story_ID,
                                stories.language AS language,
                                chapters.hidden_flag
                                FROM chapters 
                                JOIN stories ON stories.story_ID = chapters.story_ID
                                JOIN accounts ON accounts.account_ID = stories.author_ID 
                                WHERE chapters.title LIKE '%$keyword%'";
        
        $sql_stories = "SELECT  stories.story_ID,
                                stories.title,
                                stories.language,
                                stories.author_ID,
                                accounts.nickname AS author_nickname,
                                stories.thumbnail_path, 
                                stories.pubblication_time,
                                stories.total_votes,
                                stories.hidden_flag       
                                FROM stories 
                                JOIN accounts ON accounts.account_ID = stories.author_ID
                                WHERE stories.title LIKE '%$keyword%'";

    }
    else{ #Branch to take to get the container

        #Last new chapters in the range of a week of all followed stories
    $sql_chapters = "SELECT chapter_ID,
                            chapters.title, 
                            stories.title AS story_title, 
                            accounts.nickname AS author_nickname,
                            accounts.account_ID AS author_ID,
                            chapters.pubblication_time,
                            chapters.total_votes,
                            chapters.story_ID,
                            stories.language AS language,
                            chapters.hidden_flag
                            FROM chapters JOIN
                    (SELECT max(chapters.pubblication_time) AS pubblication_time 
                    FROM chapters 
                    JOIN stories ON stories.story_ID = chapters.story_ID 
                    JOIN accounts_stories ON accounts_stories.story_ID = stories.story_ID 
                    JOIN accounts ON accounts_stories.account_ID = accounts.account_ID 
                    WHERE accounts.account_ID = '$_SESSION[id]' AND chapters.pubblication_time >= DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK) 
                    GROUP BY chapters.story_ID) 
                    AS NEWEST ON chapters.pubblication_time = NEWEST.pubblication_time
                    JOIN stories ON stories.story_ID = chapters.story_ID
                    JOIN accounts ON accounts.account_ID = stories.author_ID
                    ORDER BY chapters.total_votes";

    
    #New stories in the range of a week of all followed users and of followed genres in chosen languages
    $sql_stories = "SELECT stories.story_ID,
                            stories.title,
                            stories.language,
                            stories.author_ID,
                            accounts.nickname AS author_nickname,
                            stories.thumbnail_path, 
                            stories.pubblication_time,
                            stories.total_votes,
                            stories.hidden_flag
                    FROM stories 
                    JOIN followers_followeds ON stories.author_ID = followers_followeds.followed_ID
                    JOIN accounts ON accounts.account_ID = stories.author_ID
                    WHERE followers_followeds.follower_ID = '$_SESSION[id]' AND stories.pubblication_time >= DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK)  
                    UNION
                    SELECT  stories.story_ID,
                            stories.title,
                            stories.language,
                            stories.author_ID,
                            accounts.nickname AS author_nickname,
                            stories.thumbnail_path, 
                            stories.pubblication_time,
                            stories.total_votes,
                            stories.hidden_flag
                    FROM stories JOIN  
                    (SELECT stories.story_ID, stories.pubblication_time FROM stories 
                    JOIN genres_stories ON stories.story_ID = genres_stories.story_ID 
                    JOIN accounts_genres ON genres_stories.genre_name = accounts_genres.genre_name
                    WHERE accounts_genres.account_ID = '$_SESSION[id]' AND stories.pubblication_time >= DATE_SUB(CURRENT_DATE, INTERVAL 1 WEEK)
                    GROUP BY stories.story_ID) AS selected_stories 
                    ON selected_stories.story_ID = stories.story_ID
                    JOIN accounts_languages ON accounts_languages.language_name = stories.language
                    JOIN accounts ON accounts.account_ID = stories.author_ID
                    WHERE accounts_languages.account_ID = '$_SESSION[id]'
                    ORDER BY total_votes";
    }
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
    <?php print_navbar()?>

    <div class="container">
    <?php 
        if($result = $conn->query($sql_chapters)){
            while($chapter = $result->fetch_array(MYSQLI_ASSOC)){
                if(!($chapter['hidden_flag']) || (zone_moderator($chapter['language']) || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $chapter['author_nickname'])){

                    echo '<div>
    
                    <div class="box chapter">
                                <div class="post-head">
                                <a href="chapter.php?id='.$chapter['chapter_ID'].'"><p class="post-title">'.$chapter['title'].'</p></a>
                        <p class="italic">from <a href="story.php?id='.$chapter['story_ID'].'">'.$chapter['story_title'].'</a> by <a href="profile.php?id='.$chapter['author_ID'].'">'.$chapter['author_nickname'].'</a></p>
                        </div>
                        
                        <div class="post-head">
                            <p class="italic">'.$chapter['pubblication_time'].'</p>
                            <p class="react">'.$chapter['total_votes'].'❤️</p>
                        </div>
                        </div>';
                }        
            }
        }
        ?>

    <?php
        if($result = $conn->query($sql_stories)){
            while($story = $result->fetch_array(MYSQLI_ASSOC)){
                if(!($story['hidden_flag']) || (zone_moderator($story['language']) || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $story['author_nickname'])){
                    echo '<div class="box story">
                                <div class="post-head">
                                    <a href="story.php?id='.$story['story_ID'].'"><p class="post-title">'.$story['title'].'</p></a>
                    <p class="italic">written in '.$story['language'].' by <a href="profile.php?id='.$story['author_ID'].'">'.$story['author_nickname'].'</a></p>
                    </div>
                        <img src="'.$story['thumbnail_path'].'">
                    <div class="post-head">
                        <p class="italic">'.$story['pubblication_time'].'</p>
                        <p class="react">'.$story['total_votes'].' ❤️</p>
                    </div>
                    <div class="flex-row">';
                    $sql = "SELECT genre_name from genres_stories WHERE story_ID = '$story[story_ID]'";
                    if($genres = $conn->query($sql)){    
                        while($genre = $genres->fetch_array(MYSQLI_ASSOC))
                            echo '<p class="genre">'.$genre['genre_name'].'</p>';
                    }
            echo    '</div>                
                </div>';
                }
            }
        }
    ?>
    </div>
    </div>
        
</body>
</html>