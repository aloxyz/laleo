<?php
    require_once("conn.php");
    require_once("hidden/zone_moderator.php");
    session_start();

    $story_id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT 
            stories.author AS author_nickname, 
            stories.title AS story_title,
            stories.hidden_flag AS hidden_flag,
            stories.total_votes AS total_votes,
            stories.chapters_number AS chapters_number,
            stories.thumbnail_path AS thumbnail_path,
            stories.language AS language,
            accounts.account_ID AS author_ID
        FROM stories
        JOIN accounts
        ON stories.author = accounts.nickname
        WHERE stories.story_ID = '$story_id'";

    if($result = $conn->query($sql))
        $row = $result->fetch_array(MYSQLI_ASSOC);
    
    if((!($row) || ($row['hidden_flag'] && !(zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || $_SESSION['id'] == $row['author_ID'])))){
      echo "not found";
      #header("location: hidden/story_not_found.html");
    }

    $sql = "SELECT genre_name FROM genres_stories WHERE story_ID='$story_id'";
    if($result = $conn->query($sql)){
        while($genres[] = $result->fetch_array(MYSQLI_ASSOC));
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


  $("#show").on("click", function(){

    $("#hide").attr('hidden', false);
    $("#show").attr('hidden', true);

    $.post("hidden/toggle_hide_story.php",
      {
        story_id:<?php echo $story_id;?>,
        bool:0
      }
)});

  $("#hide").on("click", function(){

    $("#show").attr('hidden', false);
    $("#hide").attr('hidden', true);   

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
  
  if(<?php echo $_SESSION['id'].'=='.$row['author_ID']?>){

    if ($(this).attr('id') == 'up') var value = true;
    else var value = '0';
    
    $(this).attr('hidden', true);
    $.post("hidden/remove_genre.php",
    {
      story_id:<?php echo $story_id;?>,
      genre:$(this).attr('value')
    }
  )
}});

  $("#delete").on("click", function(){
    
    $.get("hidden/delete_story.php?id=<?php echo $story_id ?>");     
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
              echo '<a class="col-sm-1" href="index.php"><div class="col-sm-1" id = delete value = delete>DELETE</div></a>';
            }
          ?>
        </div>
        <div class="col-sm-3">
          <?php
            if((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))){
              echo '<button class="hide" id="hide">Hide</button>';
              echo '<button class="show" id="show" hidden>Show</button>';
            }
          ?>
        </div>
    </div>
    <div class="row pt-4">
        <?php if($_SESSION['id'] == $row['author_ID'])
            echo "Click on a genre to delete it";
        ?>
    </div>
    <div class="row pt-4">
      <?php 
        foreach($genres as $genre){
        echo '<div class="col-sm-1 genre_tag" value='.$genre['genre_name'] .'>
        '.$genre['genre_name'] .'
        </div>';
        }
      ?>
      </div>
  </div>
</body>
</html>