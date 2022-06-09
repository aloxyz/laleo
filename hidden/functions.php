<?php   


  function verify_genres_errors($genres){
    global $conn;
    $sql = "SELECT genre_name FROM genres";
            $result = $conn->query($sql);
            while($genre = $result -> fetch_array(MYSQLI_ASSOC)){
                $existing_genres[] = $genre;                        #obtains all result from query in the form Array("genre_name"=>$genrename)
            }
        
            foreach ($genres as $genre) {
                if(!(in_array(array("genre_name"=>$genre), $existing_genres))){    
                    $error.="Genre ".$genre." doesn't exist<br>";
                } 
            }
            return $error;
  }

  function add_story_genres($story_id, $new_genres, $already_added_genres=[]){
    global $conn;
    foreach ($new_genres as $new_genre) 
      if(!(in_array(array("genre_name"=>$new_genre), $already_added_genres))){
      $genre = $conn->real_escape_string($genre);
      $sql = "INSERT INTO genres_stories (genre_name, story_ID) 
          VALUES ('$new_genre','$story_id')";
      $conn->query($sql);
    }
  }

  function add_account_genres($account_id, $new_genres, $already_added_genres=[]){
    global $conn;
    foreach ($new_genres as $new_genre) 
      if(!(in_array(array("genre_name"=>$new_genre), $already_added_genres))){
      $genre = $conn->real_escape_string($genre);
      $sql = "INSERT INTO accounts_genres (genre_name, account_ID) 
          VALUES ('$new_genre','$account_id')";
      $conn->query($sql);
    }
  }

  function verify_languages_errors($languages){
    global $conn;
    $error = "";
    $sql = "SELECT language_name FROM languages";
            $result = $conn->query($sql);
            while($language = $result -> fetch_array(MYSQLI_ASSOC)){
                $existing_languages[] = $language;                        #obtains all result from query in the form Array("language_name"=>$languagename)
            }
        
            foreach ($languages as $language) {
                if(!(in_array(array("language_name"=>$language), $existing_languages))){    
                    $error.="language ".$language." doesn't exist<br>";
                } 
            }
            return $error;
  }

  function add_account_languages($account_id, $new_languages, $already_added_languages=[]){
    global $conn;
    foreach ($new_languages as $new_language) 
      if(!(in_array(array("language_name"=>$new_language), $already_added_languages))){
      $language = $conn->real_escape_string($language);
      $sql = "INSERT INTO accounts_languages (language_name, account_ID) 
          VALUES ('$new_language','$account_id')";
      $conn->query($sql);
    }
  }

  function add_moderator_languages($account_id, $new_languages, $already_added_languages=[]){
    global $conn;
    foreach ($new_languages as $new_language) 
      if(!(in_array(array("language_name"=>$new_language), $already_added_languages))){
      $language = $conn->real_escape_string($language);
      $sql = "INSERT INTO moderators_languages (language_name, moderator_ID) 
          VALUES ('$new_language','$account_id')";
      $conn->query($sql);
    }
  }

    function zone_moderator($language){     #says if current user is moderatore of language

        if((strcmp($_SESSION['role'], 'moderator'))){
          return false;
      }
      else{
        global $conn;
        $user_id = $_SESSION['id'];
        $sql = "SELECT moderator_ID, language_name FROM moderators_languages WHERE language_name ='$language' AND moderator_ID = $user_id";
        if($conn->query($sql)->num_rows){
            return true;
        }
        else{
            return false;
        }
      }
    }

    function verify_mod_admin_privileges($language){
      return zone_moderator($language) || !(strcmp($_SESSION['role'], 'admin'));
  }


  function print_navbar(){
    echo'
      <div class="navbar">
        <a class="textlogo" href="index.php">Lalèo</a>
          <form action="index.php" method="GET">
              <input id="navsearch" type="text" name="keyword" placeholder="Search on Lalèo">
          </form>
          <div class="flex-row">
              <div class="navlinks">';
              if (empty($_SESSION['role'])){
                echo '<a class="link" href="login.php">Login</a>';
                echo '<a class="link" href="signup.php">Signup</a>';
              }
              else{
                echo '<a class="button" href="create_story.php">New story</a>';
                echo '<a class="button" href="create_chapter.php">New chapter</a>';
                echo '<a class="link" href="profile.php?id='.$_SESSION['id'].'">My account</a>';
                echo '<a class="link" href="logout.php">Logout</a>';
            }
    echo'  </div>
      </div>
  </div>';
}
?>