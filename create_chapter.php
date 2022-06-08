<?php
    require_once("conn.php");
    if ($_POST){

        $title = $conn->real_escape_string($_POST['title']);
        $pubblication_date = $conn->real_escape_string($_POST['pubblication_date']);
        $pubblication_hour = $conn->real_escape_string($_POST['pubblication_hour']);
        $story_ID = $conn->real_escape_string($_POST['story_ID']);
        $content = $conn->real_escape_string($_POST['content']);


        $error = "";

        #check on title
        if($title == ""){
            $error .="Title can't be empty<br>";
        }

        #check on content
        if($content == ""){
            $error .="Chapter can't be empty<br>";
        }
        
        $pubblication_time = date("Y-m-d H:i:s", strtotime("$pubblication_date $pubblication_hour"));
        if($pubblication_date != ""){
            if(strtotime($pubblication_time) < time())
                $error .= "Pubblication date can't be before today!";
        }
        else
            $pubblication_time = date("Y-m-d H:i:s");
        
        $account_id = $_SESSION['id'];
        $sql = "SELECT stories.title FROM stories WHERE story_ID='$story_ID' AND author_ID='$account_id'";
        if($result = $conn->query($sql))
            if(!($result->num_rows)){
                $error .= "This story doesn't exist or you're not its author";
        }

        if($error == ""){
            $sql = "INSERT INTO chapters (title, content, story_ID, pubblication_time) 
                    VALUES ('$title', '$content', '$story_ID', '$pubblication_time')";

            if ($result = $conn->query($sql)){
                header("location: story.php?id=".$story_ID);
            }
        }
    
    }
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <title>Sign up - Lalèo</title>
</head>
<body>
    <?php echo $error; ?>
    <form class="formbox centerbox" method="post">
        <p class="title">Create a new chapter</p>
        <ul>
            <li>
                <label for="title" class="subtitle">Chapter title</label>
                <input class="form" type="text" name="title" placeholder="Title" value=<?php echo $_POST['title']; ?> >
            </li>
            <li>
                <label for="title" class="subtitle">Content</label>
                <textarea rows="20" cols="20" name="content" placeholder="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua." value=<?php echo $_POST['content']; ?>></textarea>
            </li>
            <li>
                <label for="title" class="subtitle">Date of pubblication (default is now)</label>
                <input class="form" type="date" name="pubblication_date" value=<?php echo $_POST['pubblication_date']; ?>>
            </li>
            <li>
                <label for="title" class="subtitle">Hour of pubblication (default is now)</label>
                <input class="form" type="time" name="pubblication_hour" value=<?php echo $_POST['pubblication_hour']; ?>>
            </li>
            <li>
                <label for="title" class="subtitle">The story this chapter belongs to</label>
                <select name="story_ID" autocomplete>
                    <?php
                        $author_id = $_SESSION['id'];
                        $sql = "SELECT stories.title, stories.story_ID FROM stories WHERE author_ID= '$author_id'";
                        if($result = $conn->query($sql))
                        while ($story = $result->fetch_array(MYSQLI_ASSOC)){
                            echo'<option value='.$story['story_ID'].'>'.$story['title'].'</option>';
                        }
                    ?>
                </select>
            </li>
        </ul>   
        <input class="button" type="submit" value="Create chapter">

    </form>
</body>
</html>