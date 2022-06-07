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

            #get all existing reactions
            $sql = "SELECT reaction, reaction_code FROM reactions";
            if($result = $conn->query($sql))
                while($tmp = $result->fetch_array(MYSQLI_ASSOC))
                        $reactions[] = $tmp;

            #get all thoughts of this chapter and builds thoughts tree
            $sql = "SELECT thought_ID, thought_padre_ID FROM thoughts WHERE chapter_ID='$chapter_ID'";
            if($result = $conn->query($sql)){
                $thoughts = array(); 
                while($tmp = $result->fetch_array(MYSQLI_ASSOC)){
                    #print_r($tmp);
                        $tmp['children']=array();
                        $thoughts[$tmp['thought_ID']] = $tmp;
                    }
                    
                    print_r($thoughts);
                foreach ($thoughts as $k => &$v) {
                        if (isset($v['thought_padre_ID'])) {
                          $thoughts[$v['thought_padre_ID']]['children'][] = &$v;
                        }
                      }
                     #print_r($thoughts);
                      #print_r($thoughts);
                foreach ($thoughts as $k => $v) {
                        #echo $k;
                    if (isset($v['thought_padre_ID'])){
                        unset($thoughts[$k]);
                        #print_r($thoughts);

                        }
                      }    
                      #print_r($thoughts);               
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

  $(".react_chapter").on("click", function(){

    
    var reaction = $(this).attr('reaction');
    $(this).toggleClass("btn-primary");

    $.post("hidden/react_chapter.php",
    {
        chapter_id:<?php echo $chapter_ID;?>,
        reaction:reaction
    },
    function(data,status){
        document.getElementById("chapter_reactions_number"+reaction).innerHTML = 
        parseInt(document.getElementById("chapter_reactions_number"+reaction).innerHTML)+parseInt(data);  
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">    
    <?php echo '<title>'.$row['story_title'].' - '.$row['chapter_title'].'</title>'; ?>
</head>
<body>
        <div class="box">
            <div class="post-head">
                <a class="post-author" href="profile.php?id=<?php echo $row['author_ID'];?>"><?php echo $row['author'];?></a>
            </div>
            <p class="post-title"><?php echo $row['chapter_title'];?></p>
            <p class="post-content-preview"><?php echo $row['content'];?></p>
            <div class="reactions">
            <?php 
                foreach($reactions as $reaction){
                $reaction_code = $reaction['reaction_code'];
                $reaction = $reaction['reaction'];
                $sql1 = "SELECT * FROM chapters_accounts_reactions WHERE chapter_ID='$chapter_ID' AND reaction_code='$reaction_code'";
                $sql2 = "SELECT * FROM chapters_accounts_reactions WHERE chapter_ID='$chapter_ID' AND reaction_code='$reaction_code' AND account_ID='$_SESSION[id]'";
                if($conn->query($sql2)->num_rows)
                    echo '<div class="reactions react react_chapter btn-primary" reaction="'.$reaction_code.'">
                                <div id="chapter_reactions_number'.$reaction_code.'">'.$conn->query($sql1)->num_rows.'</div>
                            '.$reaction.'
                            </div>';
                else
                    echo '<div class="reactions react react_chapter" reaction="'.$reaction_code.'">
                                <div id="chapter_reactions_number'.$reaction_code.'">'.$conn->query($sql1)->num_rows.'</div>
                            '.$reaction.'
                            </div>';
                }
            ?>
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