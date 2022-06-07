<?php 
    require_once('conn.php'); 
    require_once('hidden/functions.php');
    session_start();
    
    $account_id = $_SESSION['id'];

    if($_POST && (($_SESSION['id'] == $account_id))){
        if($_POST['genres']){
            $genres = $_POST['genres'];
            if(!(verify_genres_errors($genres))){
                $sql = "SELECT genre_name FROM accounts_genres WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                    while($account_genres[] = $result->fetch_array(MYSQLI_ASSOC));
                }
                
                add_account_genres($account_id, $genres, $account_genres);
                
            }
        }

        if($_POST['languages']){
            $languages = $_POST['languages'];
            if(!(verify_languages_errors($languages))){
                $sql = "SELECT language_name FROM accounts_languages WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                    while($account_languages[] = $result->fetch_array(MYSQLI_ASSOC));
                }
                
                add_account_languages($account_id, $languages, $account_languages);
                
            }
        }
    }

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $(".genre_tag").on("click", function(){
  
    $(this).attr('hidden', true);
    $.post("hidden/remove_user_genre.php",
    {
      account_id:<?php echo $account_id;?>,
      genre:$(this).attr('value')
    }
  )
});

$(".language_tag").on("click", function(){

    $(this).attr('hidden', true);
    $.post("hidden/remove_user_language.php",
    {
      account_id:<?php echo $account_id;?>,
      language:$(this).attr('value')
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
    <title>Edit your genres and languages - Lalèo</title>
</head>
<body>
    <a class="textlogo" href="index.php">Lalèo</a>
    <container>
        <div class="row">
            <div class="col-sm-4">New genres:</div>
            <div class="col-sm-6">Already following genres (click on one to remove it): </div>
        </div>
<div class="row">
            <div class="col-sm-4">
                
            <form method="post">
                  <select name="genres[]" id="genre" class="form-select" multiple="multiple" autocomplete>';
                <?php    
                    $sql = 'SELECT genre_name FROM genres';
                    if($result = $conn->query($sql))
                        while ($new_genre = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$new_genre['genre_name'].'>'.$new_genre['genre_name'].'</option>';
                    }
                ?>
                  </select>
                  <div class="row"><input class="button" type="submit" value="Add Genres"></div>
            </form>

            </div>
               <?php
                $sql = "SELECT genre_name FROM accounts_genres WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                        while($genre = $result->fetch_array(MYSQLI_ASSOC)){
                            if($genre['genre_name']!='')
                                echo '<div class="col-sm-1 genre_tag" value='.$genre['genre_name'] .'>
                            '.$genre['genre_name'] .' 
                                </div>';    
                        }        
                }
                ?>
                
</div>
        <div class="row pt-5">
            <div class="col-sm-4">New languages:</div>
            <div class="col-sm-6">Already chosen languages (click on one to remove it):</div>
        </div>
<div class="row">
            <div class="col-sm-4">
                
            <form method="post">
                  <select name="languages[]" id="language" class="form-select" multiple="multiple" autocomplete>';
                <?php    $sql = 'SELECT language_name FROM languages';
                    if($result = $conn->query($sql))
                        while ($new_language = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$new_language['language_name'].'>'.$new_language['language_name'].'</option>';
                    }
                ?>
                  </select>
                  <div class="row"><input class="button" type="submit" value="Add Languages"></div>
            </form>

            </div>
                
            <?php
                $sql = "SELECT language_name FROM accounts_languages WHERE account_ID='$account_id'";
                if($result = $conn->query($sql)){
                        while($language = $result->fetch_array(MYSQLI_ASSOC)){
                            if($language['language_name']!='')
                                echo '<div class="col-sm-1 language_tag" value='.$language['language_name'] .'>
                            '.$language['language_name'] .'
                                </div>';    
                        }        
                }
            ?>            
</div>    
    </container>


</body>
</html>