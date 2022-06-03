<?php
    require_once("conn.php");
    session_start();

        $chapter_ID = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT 
            stories.author_ID AS author_ID,
            accounts.nickname AS author, 
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
        ON accounts.account_ID = stories.author_ID
        WHERE chapters.chapter_ID = '$chapter_ID'";
        #Checks if the query was succesful and if there is a row that matched the search.
        $error = true;
        if($result = $conn->query($sql))
            if($result->num_rows){
                $row = $result->fetch_array(MYSQLI_ASSOC);
                if(!(visible($row))){
                    #header("hidden/chapter_not_found.php");
                    #exit();
                }
            }
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $(".vote").on("click", function(){

    if ($(this).attr('id') == 'up') var value = true;
    else var value = '0';
    $.post("vote_chapter.php",
    {
        chapter_id:<?php echo $chapter_ID;?>,
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
    <?php echo '<title>'.$row['story_title'].'- Lalèo</title>'; ?>
</head>
<body>
        <div class="box">
            <div class="post-head">
                <a class="post-author"><?php echo $row['author'];?></a>
            </div>
            <p class="post-title"><?php echo $row['chapter_title'];?></p>
            <p class="post-content-preview"><?php echo $row['content'];?></p>
            <div class="reactions">
            <p class="react">5 ❤️</p>
            <p class="react">3 🎉</p>
            <p class="react">2 🔥</p>           
            </div>
        </div>
        <div chap_id=<?php echo $_GET['id'];?>><button class="vote" id="up">UP</button></div>
        <div id="total_votes"><?php echo $row['total_votes'];?></div>      
        <div chap_id=<?php echo $_GET['id'];?>><button class="vote" id="down">DOWN</button></div>
        <div class="thoughts">
            <p class="title">Thoughts</p>
            <div class="box">
                <div class="thought">
                    <div class="post-head">
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
            if($_SESSION['role'] == 'moderator' || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $author) #checks if current user isn't moderator nor admin nor author

                return true;
            else
                return false;
        else
            return true;
    }
?>