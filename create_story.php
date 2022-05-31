<?php 
    require_once('conn.php'); 
    session_start();

    if(!(isset($_SESSION['id'])))
        header("location: login.php");

    if($_POST){
        $title = $conn->real_escape_string($_POST['title']);
        $image = $_FILES['image'];
        $language = $conn->real_escape_string($_POST['language']);
        $genres = $_POST['genre'];

        $error = "";

        #check on title
        if($title == ""){
            $error .="Title can't be empty<br>";
        }

        print_r($image);
        #checks on image
        if($image['size']){   #if a file was uploaded
            $maxSize   = 3 * 1024 * 1024; #3 MB
            if($image['size'] > $maxSize){
                $error.="The image is too big<br>";
            }

            $accepted_types = array("image/png");
            if($image['size']!=0 && !in_array($image['type'], $accepted_types)){
                $error .= "Format not supported<br>";
            }
        }
        else{
            $image = NULL;
        }

        #checks on language
        if($language == ""){
            $error .="You must choose a langauge<br>";
        }
        else{
        $sql = "SELECT language_name FROM languages WHERE language_name='$language'";
        if (!($conn->query($sql)->num_rows))
            $error .= "That language doesn't exist<br>";
        }

        #checks on genres
        if(empty($genres)){
            $error.= "You must choose at least one genre<br>";
        }
        else{
            $sql = "SELECT genre_name FROM genres";
            $result = $conn->query($sql);
            while($genre = $result -> fetch_array(MYSQLI_ASSOC)){
                $existing_genres[] = $genre;                        #obtains all result from query in the form Array("genre_name"=>$genrename)
            }
        
            foreach ($genres as $genre) {
                if(!(in_array(array("genre_name"=>$genre), $existing_genres))){    
                    $error.="Genre".$genre."doesn't exist<br>";
                } 
            } 
        }

        if($error == ""){
            $nickname = $_SESSION['nickname'];
            $sql = "INSERT INTO stories (title, author, language) 
                    VALUES (?,?,?)";
            $query = $conn->prepare($sql);
            $query -> bind_param('sss', $title, $nickname, $language);
            $query->execute();
            
            $story_id = $conn->insert_id;
            if ($image != NULL){
                $path = "pictures/stories/".$story_id;
                move_uploaded_file($image['tmp_name'], $path);
                $sql = "UPDATE stories SET thumbnail_path = '$path' WHERE story_ID=".$story_id;
                $conn->query($sql);
            }


            foreach ($genres as $genre) {
                $genre = $conn->real_escape_string($genre);
                $sql = "INSERT INTO genres_stories (genre_name, story_ID) 
                    VALUES ('$genre','$story_id')";
                $conn->query($sql);
            }
            header("location: story.php?id=".$story_id); #goes to page with id returned from last query
        }
     }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> 
    <title>Create story - Lalèo</title>
</head>
<body>
    <a class="textlogo" href="index.php">Lalèo</a>
    <?php echo '<br>'.$error; ?>
    <div class="container rounded bg-white mt-5">
    <form method="post" enctype="multipart/form-data">
            <div class="row mt-2">
                <input type="text" class="form-control" name="title" placeholder="Title" value=<?php echo $_POST['title']; ?> >
                <input type="file" name="image">
            </div>
            <div class="row mt-2">
        <div class="container">
            <div class="row">
                <div class="col-sm">
                <select name="language" id="language" class="form-select" autocomplete>';
            <?php
                $sql = 'SELECT language_name FROM languages';
                if($result = $conn->query($sql))
                    while ($language = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$language['language_name'].'>'.$language['language_name'].'</option>';
                    }
            ?>
                </select>
                </div>
                <div class="col-sm">
                <select name="genre[]" id="genre" class="form-select" multiple="multiple" autocomplete>';
            <?php
                $sql = 'SELECT genre_name FROM genres';
                if($result = $conn->query($sql))
                    while ($genre = $result->fetch_array(MYSQLI_ASSOC)){
                        echo'<option value='.$genre['genre_name'].'>'.$genre['genre_name'].'</option>';
                    }
            ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm">
                <input class="button" type="submit" value="Create Story">
                </div>   
            </div>
        
        </div>
    </form>
</div>
</body>
</html>