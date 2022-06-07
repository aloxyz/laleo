<?php
    require_once("conn.php");
    require_once("hidden/functions.php");
    session_start();

    $story_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT 
            stories.author_ID AS author_ID,
            accounts.nickname AS author_nickname,
            stories.title AS story_title,
            stories.hidden_flag AS hidden_flag,
            stories.total_votes AS total_votes,
            stories.chapters_number AS chapters_number,
            stories.thumbnail_path AS thumbnail_path,
            stories.language AS language
        FROM stories
        JOIN accounts
        ON stories.author_ID = accounts.account_ID
        WHERE stories.story_ID = '$story_id'";
    if($result = $conn->query($sql))
        $row = $result->fetch_array(MYSQLI_ASSOC);
    
    if((!($row) || ($row['hidden_flag'] && !(zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || $_SESSION['id'] == $row['author_ID'])))){
      header("location: hidden/story_not_found.html");
    }

    $sql = "SELECT genre_name FROM genres_stories WHERE story_ID='$story_id'";
    if($result = $conn->query($sql)){
        while($story_genres[] = $result->fetch_array(MYSQLI_ASSOC));
      }


      if($_POST && (($_SESSION['id'] == $row['author_ID'] || verify_mod_admin_privileges() || $_SESSION['role']=='admin'))){   #adding genres
        if(!(verify_genres_errors($_POST['genre'])))
          add_story_genres($story_id, $_POST['genre'], $story_genres);
      }
?>  

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#follow").on("click", function(){

    $("#unfollow").attr('hidden', false);
    $("#follow").attr('hidden', true);

    $.get("hidden/follow_story.php?id=<?php echo $story_id ?>");
  });

  $("#unfollow").on("click", function(){

    $("#follow").attr('hidden', false);
    $("#unfollow").attr('hidden', true);   

    $.get("hidden/unfollow_story.php?id=<?php echo $story_id ?>");
  });


  $("#show_story").on("click", function(){

    $("#hide_story").attr('hidden', false);
    $("#show_story").attr('hidden', true);

    $('.hide_chapter').each(function(){
      $(this).attr('hidden', false);
    })

    $('.show_chapter').each(function(){
      $(this).attr('hidden', true);
    })

    $.post("hidden/toggle_hide_story.php",
      {
        story_id:<?php echo $story_id;?>,
        bool:0
      }
)});

  $("#hide_story").on("click", function(){

    $("#show_story").attr('hidden', false);
    $("#hide_story").attr('hidden', true);
    
    $('.hide_chapter').each(function(){
      $(this).attr('hidden', true);
    })

    $('.show_chapter').each(function(){
      $(this).attr('hidden', false);
    })

    $.post("hidden/toggle_hide_story.php",
      {
        story_id:<?php echo $story_id;?>,
        bool:1
      }
)});

  $(".vote").on("click", function(){
      if ($(this).attr('id') == 'up') var value = true;
      else var value = '0';

      $.post("vote_story.php",
      {
        story_id:<?php echo $story_id;?>,
        vote:value
      },
      function(data,status){
      document.getElementById("total_votes").innerHTML = parseInt(document.getElementById("total_votes").innerHTML)+parseInt(data);  
      }
)
});

  $(".genre_tag").on("click", function(){
  
  if(<?php echo $_SESSION['id'].'=='.$row['author_ID']?> || <?php echo verify_mod_admin_privileges($row['language']) ? 'true' : 'false';?>){

    $(this).attr('hidden', true);
    $.post("hidden/remove_story_genre.php",
    {
      story_id:<?php echo $story_id;?>,
      genre:$(this).attr('value')
    }
  )
}});

  $("#delete_story").on("click", function(){
    
    $.get("hidden/delete_story.php?id=<?php echo $story_id ?>");     
    });

$(".show_chapter").on("click", function(){

  var chapter_ID = $(this).attr('id');
$("#"+chapter_ID+".hide_chapter").attr('hidden', false);
$("#"+chapter_ID+".show_chapter").attr('hidden', true);

$.post("hidden/toggle_hide_chapter.php",
  {
    chapter_id:chapter_ID,
    bool:0
  }
)});

$(".hide_chapter").on("click", function(){

  var chapter_ID = $(this).attr('id');
$("#"+chapter_ID+".hide_chapter").attr('hidden', true);
$("#"+chapter_ID+".show_chapter").attr('hidden', false);

$.post("hidden/toggle_hide_chapter.php",
  {
    chapter_id:chapter_ID,
    bool:1
  }
)});

$(".delete_chapter").on("click", function(){
    
    var chapter_id = $(this).attr('id');
    $('.row.chapter.'+chapter_id).attr('hidden', true);
    $.get("hidden/delete_chapter.php?id="+chapter_id);     
    });

});



</script>

<html>
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <?php echo '<title>'.$row['story_title'].'- Lalèo</title>' ?>
</head>
<body>
  <a class="textlogo" href="index.php">Lalèo</a>
  <div class="container">
      <div class="row">
        <div class="col-sm-7">  
          <div class="row">
          <a href=<?php echo"profile.php?id=".$row['author_ID']; ?>> <?php echo $row['author_nickname'] ?><a>
          </div>
            <div class="row">
              <?php echo $row['story_title']; ?>
            </div>
            
            <div class="row">
              <div class="col-sm-1 justify-content-center">
              <button class="vote" id="up">UP</button>
              </div>
              
              <div class="col-sm-2 justify-content-center" id="total_votes"><?php echo $row['total_votes'] ?></div>
              
              <div class="col-sm-1 justify-content-center">
              <button class="vote" id="down">DOWN</button>
              </div>
            </div>
        </div>
          
        <div class="col-sm-5 align-self-center">
          <div class="row justify-content-center align-self-center">
                <?php
                  if($row['thumbnail_path']){
                    header("Contet-type: image/png");
                    echo '<img class="img-thumbnail img-fluid img-responsive" src="pictures/stories/'.$story_id.'">';
                  }
                  else
                    echo '<img src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">';
                ?>
          </div>
          <div class="row justify-content-between align-self-center">
            <div class="col-sm-5">
              <?php 
                $sql = "SELECT * FROM accounts_stories where account_ID = '$_SESSION[id]' AND story_ID = '$story_id'";
                if($result = $conn->query($sql)){
                  $tmp_row = $result->fetch_array(MYSQLI_ASSOC);
                  if($tmp_row){
                    echo '<button class="unfollow" id="unfollow">Unfollow</button>';
                    echo '<button class="follow" id="follow" hidden>Follow</button>';
                  }
                    else{
                    echo '<button class="unfollow" id="unfollow" hidden>Unfollow</button>';
                    echo '<button class="follow" id="follow">Follow</button>';
                    }
                }
              
              ?>
            </div>
          </div>
        </div>
    </div>
    <div class="row pt-5">
        <div class="col-sm-6">
        <?php echo $row['language']; ?>
        </div>
        <div class="col-sm-3">
          <?php 
            if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || $_SESSION['id'] == $row['author_ID'])){
              echo '<a class="col-sm-1" href="index.php"><div class="col-sm-1" id ="delete_story" value = delete>DELETE</div></a>';
            }
          ?>
        </div>
        <div class="col-sm-3">
          <?php
            if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))){
              if($row['hidden_flag'] == false){
                echo '<button class="hide" id="hide_story">Hide</button>';
                echo '<button class="show" id="show_story" hidden>Show</button>';
              }
              else{
                echo '<button class="hide" id="hide_story" hidden>Hide</button>';
                echo '<button class="show" id="show_story">Show</button>';
              }
            }
          ?>
        </div>
    </div>
    <div class="row pt-4">
        <?php if($_SESSION['id'] == $row['author_ID'] || verify_mod_admin_privileges() || $_SESSION['role']=='admin')
            echo '<div class="col-sm-6">Click on a genre to delete it</div>';
            echo '<div class="col-sm-6">
                  <label for="genre">Add new genres</label>
                  <form method="post">
                  <select name="genre[]" id="genre" class="form-select" multiple="multiple" autocomplete>';
                    $sql = 'SELECT genre_name FROM genres';
                    if($result = $conn->query($sql))
                        while ($new_genre = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$new_genre['genre_name'].'>'.$new_genre['genre_name'].'</option>';
                    }
            echo    '</select>
                  <div class="row"><input class="button" type="submit" value="Add Genres"></div>
                  </form>
                  </div>';
        ?>
    </div>
    <div class="row pt-4">
      <?php 
        foreach($story_genres as $genre){
          if($genre['genre_name']!='')
        echo '<div class="col-sm-1 genre_tag" value='.$genre['genre_name'] .'>
        '.$genre['genre_name'] .'
        </div>';
        }
      ?>
      </div>

      <?php
      if($_SESSION['nickname'] == $row['author_nickname'])
        echo '<div class="row pt-4"><a href="create_chapter.php">New Chapter</a></div>';
      ?>        

      <?php #what chapters to show to who can see hidden chapters
      if(zone_moderator($row['language']) || $_SESSION['role']=="admin" || $row['author_nickname'] == $_SESSION['nickname']){
            echo '<div class="row pt-4">
                  <div class="col-sm-2"></div>
                  <div class="col-sm-2"></div>
                  <div class="col-sm-4">Title</div>
                  <div class="col-sm-4">Total Votes</div>
                  </div>';
            
            $sql = "SELECT title, total_votes, hidden_flag, chapter_ID FROM chapters WHERE story_ID='$story_id'";
            if($result = $conn->query($sql))
                    while ($chapter = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<div class="row chapter '.$chapter['chapter_ID'].'"">
                                <div class="col-sm-2">';
                              if(zone_moderator($row['language']) || $_SESSION['role']=="admin"){
                                if($chapter['hidden_flag'] == false){
                                echo '<button class="hide_chapter" id="'.$chapter['chapter_ID'].'">Hide</button>';
                                echo '<button class="show_chapter" id="'.$chapter['chapter_ID'].'" hidden>Show</button>';
                                }
                                else{
                                  echo '<button class="hide_chapter" id="'.$chapter['chapter_ID'].'" hidden>Hide</button>';
                                  echo '<button class="show_chapter" id="'.$chapter['chapter_ID'].'">Show</button>';
                                }
                              }
                        echo'</div>';
                        echo'   <div class="col-sm-2">
                                <button class="delete_chapter" id="'.$chapter['chapter_ID'].'">Delete</button>
                                </div>';
                        echo'   <div class="col-sm-4">
                                    <a href="chapter.php?id='.$chapter['chapter_ID'].'">'.$chapter['title'].'</a> 
                                </div>
                                <div class="col-sm-4">
                                    '.$chapter['total_votes'].'
                                </div>
                            </div>';
                    }
                  }
            else{ #what to show to everyone else
            echo '<div class="row pt-4">
                    <div class="col-sm-3">Title</div>
                    <div class="col-sm-6">Total Votes</div>
                  </div>';
            
            $sql = "SELECT title, total_votes, chapter_ID FROM chapters WHERE story_ID='$story_id' AND hidden_flag=0";
            if($result = $conn->query($sql))
                    while ($chapter = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<div class="row">
                                <div class="col-sm-3">
                                    <a href="chapter.php?id='.$chapter['chapter_ID'].'">'.$chapter['title'].'</a> 
                                </div>
                                <div class="col-sm-6">
                                    '.$chapter['total_votes'].'
                                </div>
                            </div>';
                    }
                  }
      ?>
  </div>
</body>
</html>