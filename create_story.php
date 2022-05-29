<?php 
    require_once('conn.php'); 
    session_start();

    if(!(isset($_SESSION['id'])))
        header("location: login.php");

    if($_POST){
        $title = $conn->real_escape_string($_POST['title']);
        $image = $_FILES['image'];
        $language = $conn->real_escape_string($_POST['language']);

        $maxSize   = 3 * 1024 * 1024; #3 MB
        $error = "";
        if($image['size'] > $maxSize){
            $error.="The image is too big<br>";
        }

        $accepted_types = array("image/jpeg", "image/png");

        if(!in_array($image['type'], $accepted_types)){
            $error .= "Format not supported<br>";
        }

        $sql = "SELECT language_name FROM languages WHERE language_name='$language'";
        if (!($conn->query($sql)->num_rows))
            $error .= "That language doesn't exist<br>";

        if($error == ""){
            $nickname = $_SESSION['nickname'];
            $image = $conn->real_escape_string(file_get_contents($image['tmp_name']));
            $sql = "INSERT INTO stories (title, author, thumbnail, language) 
                    VALUES ('$title', '$nickname', '$image', '$language')";

            $conn->query($sql);
        }

    echo $image;   
    print_r($image);

    }


?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"> 
    <title>Create story - Lalèo</title>
</head>
<body>
    <a class="textlogo" href="index.php">Lalèo</a>
    <?php echo '<br>'.$error; ?>
    <div class="container rounded bg-white mt-5">
    <form method="post" enctype="multipart/form-data">
        <div>
            <div class="row mt-2">
                <input type="text" class="form-control" name="title" placeholder="Title" value=<?php echo $_POST['title']; ?> >
                <input type="file" name="image">
            </div>
            <div class="row mt-2">
                <select name="language" id="language" class="form-select">';
            <?php
                $sql = 'SELECT language_name FROM languages';
                if($result = $conn->query($sql))
                    while ($language = $result->fetch_array(MYSQLI_ASSOC)){
                        echo $language;
                        echo'<option value='.$language['language_name'].'>'.$language['language_name'].'</option>';
                    }
            ?>
                </select>
            <input class="button" type="submit" value="Create Story">
            </div>
        </div>
    </form>
</div>
</body>
</html>