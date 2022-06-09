<?php 
    require_once('conn.php'); 
    require_once('hidden/functions.php');
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
        if(isset($title)){
            $error .="Title can't be empty<br>";
        }

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
            $error = verify_genres_errors($genres);
        }

        if($error == ""){
            $author_ID = $_SESSION['id'];
            $sql = "INSERT INTO stories (title, author_ID, language) 
                    VALUES (?,?,?)";
            $query = $conn->prepare($sql);
            $query -> bind_param('sss', $title, $author_ID, $language);
            $query->execute();
            
            $story_id = $conn->insert_id;
            if ($image != NULL){
                $path = "pictures/stories/".$story_id;
                move_uploaded_file($image['tmp_name'], $path);
                $sql = "UPDATE stories SET thumbnail_path = '$path' WHERE story_ID=".$story_id;
                $conn->query($sql);
            }

            add_story_genres($story_id, $genres);
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
    <title>Front page - Lalèo</title>
</head>
<body>

    <?php print_navbar()?>

    <?php echo '<br>'.$error; ?>
    <div class="container">
        <p class="title">Create a new story</p>
    <form method="post" enctype="multipart/form-data">     
        <ul>
            <li>
                <label for="title">Story title</label>
                <input type="text" trol" name="title" placeholder="Title" value=<?php echo $_POST['title']; ?> >
            </li>
            <li>
                <label for="image">Choose an image for your story</label>
                <input type="file" name="image">
            </li>
            <li>
                <label for="image">Select one or more genres</label>
                <select name="genre[]" id="genre" multiple="multiple" autocomplete>
                    <?php
                        $sql = 'SELECT genre_name FROM genres';
                        if($result = $conn->query($sql))
                            while ($genre = $result->fetch_array(MYSQLI_ASSOC)){
                                echo'<option value='.$genre['genre_name'].'>'.$genre['genre_name'].'</option>';
                            }
                    ?>  
                </select>
            </li>
            <li>
                <label for="image">Select the language the story is written in</label>
                <select name="language" id="language" autocomplete>
                    <?php
                        $sql = 'SELECT language_name FROM languages';
                        if($result = $conn->query($sql))
                            while ($language = $result->fetch_array(MYSQLI_ASSOC)){
                                echo'<option value='.$language['language_name'].'>'.$language['language_name'].'</option>';
                            }
                    ?>
                </select>
            </li>
            <li>
                <input class="button" type="submit" value="Create story">
            </li>
        </ul>
    </form>
</div>
</body>
</html>