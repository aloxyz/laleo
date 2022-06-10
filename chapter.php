<?php
    require_once("conn.php");
    require_once("hidden/functions.php");
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
            $sql = "SELECT reaction FROM reactions";
            if($result = $conn->query($sql))
                while($tmp = $result->fetch_array(MYSQLI_ASSOC))
                        $reactions[] = $tmp;


            if($_POST['root_reply'] && $_POST['root_reply']!="" && $_SESSION){
                try{
                    $root_reply = $conn->real_escape_string($_POST['root_reply']);
                    $sql = "INSERT INTO thoughts (content, chapter_ID, author) VALUES ('$root_reply','$chapter_ID','$_SESSION[nickname]')";
                    $conn->query($sql);
                }
                catch(Exception $e){
                    echo "Something went wrong";
                }
            }

            if($_POST['thought_reply'] && $_POST['thought_reply']!="" && $_SESSION){
                try{
                    $thought_reply = $conn->real_escape_string($_POST['thought_reply']);
                    $thought_padre_id = $conn->real_escape_string($_POST['thought_padre_id']);
                    $sql = "INSERT INTO thoughts (content, chapter_ID, author, thought_padre_ID) VALUES ('$thought_reply','$chapter_ID','$_SESSION[nickname]','$thought_padre_id')";
                    $conn->query($sql);
                }
                catch(Exception $e){
                    echo "Something went wrong";
                }
            }
                


            #get all thoughts of this chapter and builds thoughts tree
            $sql = "SELECT * FROM thoughts WHERE chapter_ID='$chapter_ID'";
            if($result = $conn->query($sql)){
                $thoughts = array(); 
                while($tmp = $result->fetch_array(MYSQLI_ASSOC)){
                        $tmp['children']=array();
                        $thoughts[$tmp['thought_ID']] = $tmp;
                    }
                
                #puts children in place
                foreach ($thoughts as $k => &$v) {
                        if (isset($v['thought_padre_ID'])) {
                          $thoughts[$v['thought_padre_ID']]['children'][] = &$v;
                        }
                      }

                #deletes all children from root
                foreach ($thoughts as $k => &$v) {
                    if (isset($v['thought_padre_ID'])){
                        unset($thoughts[$k]);
                        }               
                      }
                    }

                #prints comments structure
                function display_comments(array $comments, $level = 0) {
                    global $conn;        
                    global $chapter_ID;
                    $sql = "SELECT stories.language AS language FROM stories JOIN chapters ON stories.story_ID = chapters.story_ID WHERE chapter_ID='$chapter_ID'";
                    $row = $conn->query($sql)->fetch_array(MYSQLI_ASSOC);
                    
                    foreach ($comments as $thought) {
                    echo '<div class="row">';
                        for($i=0; $i<$level*15;$i++) echo '&nbsp;';
                    echo    '<div class="thought box">
                                <div class="post-head">
                                    <p class="post-author">'.$thought['author'].'</p>
                                </div>
                                <div class="post-content">';
                                if(!($thought['hidden_flag']) || (zone_moderator($row['language']) || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $thought['author']))
                                    echo $thought['content'];
                                else 
                                    echo 'HIDDEN';
                    echo    '</div>
                                <div class="reactions">';
                                if (zone_moderator($row['language']) || $_SESSION['role'] == 'admin' || $_SESSION['nickname'] == $thought['author'])
                                    echo '<button class="delete_thought" id="'.$thought['thought_ID'].'">Delete</button>';
                            
                            if(zone_moderator($row['language']) || $_SESSION['role']=="admin"){
                                if($thought['hidden_flag'] == false){
                                echo '<button class="hide_thought" id="'.$thought['thought_ID'].'">Hide</button>';
                                echo '<button class="show_thought" id="'.$thought['thought_ID'].'" hidden>Show</button>';
                                }
                                else{
                                  echo '<button class="hide_thought" id="'.$thought['thought_ID'].'" hidden>Hide</button>';
                                  echo '<button class="show_thought" id="'.$thought['thought_ID'].'">Show</button>';
                                }
                              }

                                global $reactions;
                                foreach($reactions as $reaction){
                                    $reaction = $reaction['reaction'];
                                    $sql1 = "SELECT * FROM thoughts_accounts_reactions WHERE thought_ID='$thought[thought_ID]' AND reaction='$reaction'";
                                    $sql2 = "SELECT * FROM thoughts_accounts_reactions WHERE thought_ID='$thought[thought_ID]' AND reaction='$reaction' AND account_ID='$_SESSION[id]'";
                                    if($conn->query($sql2)->num_rows)
                                        echo '<div class="reactions react react_thought btn-primary" thought_ID="'.$thought['thought_ID'].'" reaction="'.$reaction.'">
                                                <div id="thought_reactions_number'.$reaction.$thought['thought_ID'].'">'.$conn->query($sql1)->num_rows.'</div>
                                                '.$reaction.'
                                                </div>';
                                    else
                                        echo '<div class="reactions react react_thought" thought_ID="'.$thought['thought_ID'].'" reaction="'.$reaction.'">
                                                <div id="thought_reactions_number'.$reaction.$thought['thought_ID'].'">'.$conn->query($sql1)->num_rows.'</div>
                                                '.$reaction.'
                                                </div>';
                                }
                            echo      '</div>
                                    <button class="thought_comment_button" thought_id="'.$thought['thought_ID'].'">Reply</button>    
                                    <div class="row pt-3" id="thought_reply'.$thought['thought_ID'].'" style="display:none">
                                        <form method="post">
                                            <textarea cols="80" rows="10" class="thought_comment" name="thought_reply"></textarea>
                                            <input type="hidden" id="thought_id" name="thought_padre_id" value='.$thought['thought_ID'].'>
                                            <input class="button" type="submit" value="Comment">              
                                        </form>
                                    </div>
                                    </div>
                                </div>';
                      if (!empty($thought['children'])) {
                        display_comments($thought['children'], $level + 1);
                      }
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

    $(".react_thought").on("click", function(){

        var reaction = $(this).attr('reaction');
        var thought_ID = $(this).attr('thought_ID');
        $(this).toggleClass("btn-primary");

        $.post("hidden/react_thought.php",
        {
            thought_id:thought_ID,
            reaction:reaction
        },
        function(data,status){
            document.getElementById("thought_reactions_number"+reaction+thought_ID).innerHTML = 
            parseInt(document.getElementById("thought_reactions_number"+reaction+thought_ID).innerHTML)+parseInt(data);  
        }
        )
    });

    $(".show_thought").on("click", function(){

        var thought_ID = $(this).attr('id');
        $("#"+thought_ID+".hide_thought").attr('hidden', false);
        $("#"+thought_ID+".show_thought").attr('hidden', true);

        $.post("hidden/toggle_hide_thought.php",
        {
        thought_id:thought_ID,
        bool:0
        }
    )});

    $(".hide_thought").on("click", function(){

        var thought_ID = $(this).attr('id');
        $("#"+thought_ID+".hide_thought").attr('hidden', true);
        $("#"+thought_ID+".show_thought").attr('hidden', false);

        $.post("hidden/toggle_hide_thought.php",
        {
        thought_id:thought_ID,
        bool:1
        }
    )});

    $(".delete_thought").on("click", function(){
    
    var thought_id = $(this).attr('id');
    $.get("hidden/delete_thought.php?id="+thought_id);
    });

    $(".chapter_comment_button").on("click", function(){
    
        $('#chapter_comment').toggle();
    });

    $(".thought_comment_button").on("click", function(){
    
    var thought_id = $(this).attr("thought_id"); 
    $('#thought_reply'+thought_id).toggle();
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
        <?php print_navbar()?>
        <div class="box">
            <div class="post-head">
                <a class="post-author" href="profile.php?id=<?php echo $row['author_ID'];?>"><?php echo $row['author'];?></a>
            </div>
            <p class="post-title"><?php echo $row['chapter_title'];?></p>
            <p class="post-content-preview"><?php echo $row['content'];?></p>
            <div class="reactions">
            <?php 
                foreach($reactions as $reaction){
                $reaction = $reaction['reaction'];
                $sql1 = "SELECT * FROM chapters_accounts_reactions WHERE chapter_ID='$chapter_ID' AND reaction='$reaction'";
                $sql2 = "SELECT * FROM chapters_accounts_reactions WHERE chapter_ID='$chapter_ID' AND reaction='$reaction' AND account_ID='$_SESSION[id]'";
                if($conn->query($sql2)->num_rows)
                    echo '<div class="reactions react react_chapter btn-primary" reaction="'.$reaction.'">
                                <div id="chapter_reactions_number'.$reaction.'">'.$conn->query($sql1)->num_rows.'</div>
                            '.$reaction.'
                            </div>';
                else
                    echo '<div class="reactions react react_chapter" reaction="'.$reaction.'">
                                <div id="chapter_reactions_number'.$reaction.'">'.$conn->query($sql1)->num_rows.'</div>
                            '.$reaction.'
                            </div>';
                }
            ?>
            </div>
            <button class="chapter_comment_button">Reply</button>    
            <div class="row pt-3" id="chapter_comment" style="display:none">
                <form method="post">
                    <textarea cols="80" rows="10" class="chapter_comment" name="root_reply"></textarea>
                    <input class="button" type="submit" value="Comment">                
                </form>
            </div>
        </div>
        <div chap_id=<?php echo $_GET['id'];?>><button class="vote" id="up">UP</button></div>
        <div id="total_votes"><?php echo $row['total_votes'];?></div>      
        <div chap_id=<?php echo $_GET['id'];?>><button class="vote" id="down">DOWN</button></div>
        <div class="thoughts">
            <?php display_comments($thoughts); ?>
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