<?php
    require_once("conn.php");
    session_start();
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $(".vote").on("click", function(){

    if ($(this).attr('id') == 'up') var value = true;
    else var value = '0';

    $.post("vote_chapter.php",
    {
        chapter_id:$(this).parent().attr('chap_id'),
        vote:value
    },
    function(data,status){
        document.getElementById("total_votes").innerHTML = parseInt(document.getElementById("total_votes").innerHTML)+parseInt(data);  
    }
    )
  });
});

</script>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <?php
        $chapter_ID = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT 
            stories.author AS author, 
            chapters.content AS content,
            chapters.title AS chapter_title,
            stories.title AS story_title,
            chapters.hidden_flag AS hidden_flag,
            chapters.pubblication_time AS pubblication_time,
            chapters.total_votes AS total_votes
        FROM chapters
        JOIN stories
        ON chapters.story_ID = stories.story_ID
        JOIN accounts
        ON accounts.nickname = stories.author
        WHERE chapters.chapter_ID = '$chapter_ID'";
        #Checks if the query was succesful and if there is a row that matched the search.
        $error = true;
        if($result = $conn->query($sql))
            if($result->num_rows){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                if(visible($row)){
                    $error = false;
                    echo '
    <title>'.$row['story_title'].'- Lalèo</title>
</head>
<body>
                        <div class="box">
                            <div class="post-head">
                                <img class="post-profile-picture" src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">
                                <a class="post-author">'.$row['author'].'</a>
                            </div>
                            <p class="post-title">'.$row['chapter_title'].'</p>
                            <p class="post-content-preview">'.$row['content'].'</p>';
                    echo '<div class="reactions">';
                    echo '
                    <p class="react">5 ❤️</p>
                    <p class="react">3 🎉</p>
                    <p class="react">2 🔥</p>
                    ';
                    echo '</div>
                        </div>
                        ';
                
                echo '<div chap_id='.$_GET['id'].'><button class="vote" id="up">UP</button></div>
                        <div id=total_votes>'.$row['total_votes'].'</div>      
                        <div chap_id='.$_GET['id'].'><button class="vote" id="down">DOWN</button></div>
                    ';
                }    
            }

        if($error){
            echo'
    <title>Not Found - Lalèo</title>
</head>
<body>
            <div class="box"> Chapter not found </div>';
        }
    ?>
    
    <div class="thoughts">
        <p class="title">Thoughts</p>
        <div class="box">
            <div class="thought">
                <div class="post-head">
                    <img class="post-profile-picture" src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">
                    <p class="post-author">Pippo Baudo</p>
                </div>
                <div class="post-content">
                    Very nice story, I enjoyed it a lot :)
                </div>
                <div class="reactions">
                    <p class="react">2 ❤️</p>
                </div>
            </div>
        </div>
        <div class="box">
            <div class="thought">
                <div class="post-head">
                    <img class="post-profile-picture" src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">
                    <p class="post-author">Claudio Bisio</p>
                </div>
                <div class="post-content">
                    This is some lame ass shit bro
                </div>
                <div class="reactions">
                    <p class="react">6 💩</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
    function visible($row){
       
        $pubblication_time = $row['pubblication_time'];
        $hidden = $row['hidden_flag'];
        $author = $row['author'];
    
        if($hidden || (strtotime($pubblication_time) > time())) #checks if hidden to the public
            #checks if current user isn't moderator nor admin nor author
            if($_SESSION['role'] == 'moderator' || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $author)
                return true;
            else
                return false;
        else
            return true;
    }
?>