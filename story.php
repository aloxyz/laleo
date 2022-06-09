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
            stories.thumbnail_path AS thumbnail_path,
            stories.language AS language
        FROM stories
        JOIN accounts
        ON stories.author_ID = accounts.account_ID
        WHERE stories.story_ID = '$story_id'";
if ($result = $conn->query($sql))
    $row = $result->fetch_array(MYSQLI_ASSOC);

if ((!($row) || ($row['hidden_flag'] && !(zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || $_SESSION['id'] == $row['author_ID'])))) {
    header("location: hidden/story_not_found.html");
}

$sql = "SELECT genre_name FROM genres_stories WHERE story_ID='$story_id'";
if ($result = $conn->query($sql)) {
    while ($story_genres[] = $result->fetch_array(MYSQLI_ASSOC));
}


if ($_POST && (($_SESSION['id'] == $row['author_ID'] || verify_mod_admin_privileges() || $_SESSION['role'] == 'admin'))) {   #adding genres
    if (!(verify_genres_errors($_POST['genre'])))
        add_story_genres($story_id, $_POST['genre'], $story_genres);
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#follow").on("click", function() {

            $("#unfollow").attr('hidden', false);
            $("#follow").attr('hidden', true);

            $.get("hidden/follow_story.php?id=<?php echo $story_id ?>");
        });

        $("#unfollow").on("click", function() {

            $("#follow").attr('hidden', false);
            $("#unfollow").attr('hidden', true);

            $.get("hidden/unfollow_story.php?id=<?php echo $story_id ?>");
        });


        $("#show_story").on("click", function() {

            $("#hide_story").attr('hidden', false);
            $("#show_story").attr('hidden', true);

            $('.hide_chapter').each(function() {
                $(this).attr('hidden', false);
            })

            $('.show_chapter').each(function() {
                $(this).attr('hidden', true);
            })

            $.post("hidden/toggle_hide_story.php", {
                story_id: <?php echo $story_id; ?>,
                bool: 0
            })
        });

        $("#hide_story").on("click", function() {

            $("#show_story").attr('hidden', false);
            $("#hide_story").attr('hidden', true);

            $('.hide_chapter').each(function() {
                $(this).attr('hidden', true);
            })

            $('.show_chapter').each(function() {
                $(this).attr('hidden', false);
            })

            $.post("hidden/toggle_hide_story.php", {
                story_id: <?php echo $story_id; ?>,
                bool: 1
            })
        });

        $(".vote").on("click", function() {
            if ($(this).attr('id') == 'up') var value = true;
            else var value = '0';

            $.post("vote_story.php", {
                    story_id: <?php echo $story_id; ?>,
                    vote: value
                },
                function(data, status) {
                    document.getElementById("total_votes").innerHTML = parseInt(document.getElementById("total_votes").innerHTML) + parseInt(data);
                }
            )
        });

        $(".genre_tag").on("click", function() {

            if (<?php echo $_SESSION['id'] . '==' . $row['author_ID'] ?> || <?php echo verify_mod_admin_privileges($row['language']) ? 'true' : 'false'; ?>) {

                $(this).attr('hidden', true);
                $.post("hidden/remove_story_genre.php", {
                    story_id: <?php echo $story_id; ?>,
                    genre: $(this).attr('value')
                })
            }
        });

        $("#delete_story").on("click", function() {

            $.get("hidden/delete_story.php?id=<?php echo $story_id ?>");
        });

        $(".show_chapter").on("click", function() {

            var chapter_ID = $(this).attr('id');
            $("#" + chapter_ID + ".hide_chapter").attr('hidden', false);
            $("#" + chapter_ID + ".show_chapter").attr('hidden', true);

            $.post("hidden/toggle_hide_chapter.php", {
                chapter_id: chapter_ID,
                bool: 0
            })
        });

        $(".hide_chapter").on("click", function() {

            var chapter_ID = $(this).attr('id');
            $("#" + chapter_ID + ".hide_chapter").attr('hidden', true);
            $("#" + chapter_ID + ".show_chapter").attr('hidden', false);

            $.post("hidden/toggle_hide_chapter.php", {
                chapter_id: chapter_ID,
                bool: 1
            })
        });

        $(".delete_chapter").on("click", function() {

            var chapter_id = $(this).attr('id');
            $('.row.chapter.' + chapter_id).attr('hidden', true);
            $.get("hidden/delete_chapter.php?id=" + chapter_id);
        });

    });
</script>

<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <?php echo '<title>' . $row['story_title'] . ' - Lalèo</title>' ?>
</head>

<body>
    <div class="box story">
        <div>
            <a class="post-author" href=<?php echo "profile.php?id=" . $row['author_ID']; ?>> <?php echo $row['author_nickname'] ?></a>
            <?php
            $sql = "SELECT * FROM accounts_stories where account_ID = '$_SESSION[id]' AND story_ID = '$story_id'";
            if ($result = $conn->query($sql)) {
                $tmp_row = $result->fetch_array(MYSQLI_ASSOC);
                if ($tmp_row) {
                    echo '<button class="button unfollow" id="unfollow">Unfollow</button>';
                    echo '<button class="button follow" id="follow" hidden>Follow</button>';
                } else {
                    echo '<button class="button unfollow" id="unfollow" hidden>Unfollow</button>';
                    echo '<button class="button follow" id="follow">Follow</button>';
                }
            }
            ?>
        </div>

        <div class="flex-row flex-center flex-space">
            <p class="post-title"><?php echo $row['story_title']; ?></p>
            <?php
            if ($_SESSION['nickname'] == $row['author_nickname'])
                echo '<div><a href="create_chapter.php">New Chapter</a></div>';
            ?>
            <?php
            if ((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')) || $_SESSION['id'] == $row['author_ID'])) {
                echo '<a href="index.php"><div id ="delete_story" value = delete>Delete story</div></a>';
            }
            ?>

        </div>

        <?php
        if ($row['thumbnail_path']) {
            header("Contet-type: image/png");
            echo '<img class="img-thumbnail img-fluid img-responsive" src="pictures/stories/' . $story_id . '">';
        } else
            echo '<img src="https://www.utas.edu.au/__data/assets/image/0013/210811/varieties/profile_image.png">';
        ?>




        <div class="flex-row flex-center flex-space">
            <?php echo '<p>Language: ' . $row['language'] . '</p>'; ?>
            <div class="flex-row flex-center">
                <button class="vote button" id="up">upvote</button>
                <?php echo '<p class="subtitle">' . $row['total_votes'] . '</p>' ?>
                <button class="vote button" id="down">downvote</button>
            </div>
            <div>

                <div class="flex-row">
                    <?php
                    foreach ($story_genres as $genre) {
                        if ($genre['genre_name'] != '')
                            echo '<p class="genre_tag genre">' . $genre['genre_name'] . '</p>';
                    }
                    ?>
                </div>

            </div>
        </div>







        <?php
        if ((zone_moderator($row['language']) || !(strcmp($_SESSION['role'], 'admin')))) {
            if ($row['hidden_flag'] == false) {
                echo '<button class="hide" id="hide_story">Hide</button>';
                echo '<button class="show" id="show_story" hidden>Show</button>';
            } else {
                echo '<button class="hide" id="hide_story" hidden>Hide</button>';
                echo '<button class="show" id="show_story">Show</button>';
            }
        }
        ?>


        <div>
            <?php if ($_SESSION['id'] == $row['author_ID'] || verify_mod_admin_privileges($row['language']) || $_SESSION['role'] == 'admin') {
                echo '<div class="italic flex-row flex-row-rev">Click on a genre to delete it</div>';
            }
            echo '<div>
        <label for="genre">Add new genres</label>
        <form method="post">
        <select name="genre[]" id="genre" class="form-select" multiple="multiple" autocomplete>';
            $sql = 'SELECT genre_name FROM genres';
            if ($result = $conn->query($sql))
                while ($new_genre = $result->fetch_array(MYSQLI_ASSOC)) {
                    echo '<option value=' . $new_genre['genre_name'] . '>' . $new_genre['genre_name'] . '</option>';
                }
            echo    '</select>
        <div><input class="button" type="submit" value="Add Genres"></div>
        </form>
        </div>';
            ?>

        </div>



    </div>
    <div class="box">
        <?php #what chapters to show to who can see hidden chapters
        if (zone_moderator($row['language']) || $_SESSION['role'] == "admin" || $row['author_nickname'] == $_SESSION['nickname']) {
            echo '<div>
                  <div></div>
                  <div></div>
                  </div>';
            $sql = "SELECT title, total_votes, hidden_flag, chapter_ID FROM chapters WHERE story_ID='$story_id'";
            if ($result = $conn->query($sql))
                while ($chapter = $result->fetch_array(MYSQLI_ASSOC)) {
                    echo '<div class="row chapter ' . $chapter['chapter_ID'] . '"">
                                <div>';
                    if (zone_moderator($row['language']) || $_SESSION['role'] == "admin") {
                        if ($chapter['hidden_flag'] == false) {
                            echo '<button class="hide_chapter" id="' . $chapter['chapter_ID'] . '">Hide</button>';
                            echo '<button class="show_chapter" id="' . $chapter['chapter_ID'] . '" hidden>Show</button>';
                        } else {
                            echo '<button class="hide_chapter" id="' . $chapter['chapter_ID'] . '" hidden>Hide</button>';
                            echo '<button class="show_chapter" id="' . $chapter['chapter_ID'] . '">Show</button>';
                        }
                    }
                    echo '</div>';
                    echo '   <div class="flex-row-rev flex-center flex-space">
                                <a class="subtitle" href="chapter.php?id=' . $chapter['chapter_ID'] . '">' . $chapter['title'] . '</a> 
                                
                                <button class="delete_chapter" id="' . $chapter['chapter_ID'] . '">Delete</button>
                                
                                </div>
                                    <p class="italic"> Total votes: ' . $chapter['total_votes'] . '</p>
                            </div>';
                }
        } else { #what to show to everyone else
            echo '<div>
                    <div>Title</div>
                    <div>Total Votes</div>
                  </div>';

            $sql = "SELECT title, total_votes, chapter_ID FROM chapters WHERE story_ID='$story_id' AND hidden_flag=0";
            if ($result = $conn->query($sql))
                while ($chapter = $result->fetch_array(MYSQLI_ASSOC)) {
                    echo '<div class="box">
                                <div>
                                    <a href="chapter.php?id=' . $chapter['chapter_ID'] . '">' . $chapter['title'] . '</a> 
                                </div>
                                <div>
                                    ' . $chapter['total_votes'] . '
                                </div>
                            </div>';
                }
        }
        ?>
    </div>
</body>

</html>