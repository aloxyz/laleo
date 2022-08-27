<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="style.css" rel="stylesheet">
    <?php echo '<title>' . $row['nickname'] . ' - Lalèo</title>' ?>
</head>
<?php
require_once("conn.php");
require_once("hidden/functions.php");
session_start();


if (!(isset($_SESSION['id'])))
    header("location: login.php");
$account_id = $conn->real_escape_string($_GET['id']);
$sql = "SELECT * FROM accounts WHERE account_ID = '$account_id'";

if ($result = $conn->query($sql))
    $row = $result->fetch_array(MYSQLI_ASSOC);

if ((!($row))) {
    header("location: hidden/user_not_found.php");
}

?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#follow").on("click", function() {

            $("#unfollow").attr('hidden', false);
            $("#follow").attr('hidden', true);

            $.get("hidden/follow_account.php?id=<?php echo $account_id ?>");
        });

        $("#unfollow").on("click", function() {

            $("#follow").attr('hidden', false);
            $("#unfollow").attr('hidden', true);

            $.get("hidden/unfollow_account.php?id=<?php echo $account_id ?>");
        });
    });
</script>

<body>
    <?php print_navbar()?>
        <div class="container">
        <div class="box flex-column">
            <div class="flex-row flex-center flex-space">
            <?php if (isset($row['nickname'])) echo '<p class="post-title">'.$row['name'].' '.$row['surname'].'</p>';
                if ($_SESSION['id'] == $row['account_ID'] || $_SESSION['role'] == 'admin') {
                    echo '<a href="edit_profile.php?id='.$row['account_ID'].'">Edit profile</a>';
                    }
            ?>
            </div>
                
                
                <div class="flex-row flex-center flex-space">
                    <a class="button" href="followers.php?id=<?php echo $account_id; ?>">Followers</a>
                    <a class="button" href="followed_users.php?id=<?php echo $account_id; ?>">Following</a>
                    <a class="button" href="created_stories.php?id=<?php echo $account_id; ?>">Created Stories</a>
                    <a class="button" href="followed_stories.php?id=<?php echo $account_id; ?>">Followed Stories</a>

                    <?php
                    $sql = "SELECT * FROM followers_followeds where follower_ID = '$_SESSION[id]' AND followed_ID = '$account_id'";
                    if ($result = $conn->query($sql)) {
                        $tmp_row = $result->fetch_array(MYSQLI_ASSOC);
                        if ($_SESSION['id'] != $account_id) {
                            if ($tmp_row) {
                                echo '<button class="button unfollow" id="unfollow">Unfollow</button>';
                                echo '<button class="button follow" id="follow" hidden>Follow</button>';
                            } else {
                                echo '<button class="button unfollow" id="unfollow" hidden>Unfollow</button>';
                                echo '<button class="button follow" id="follow">Follow</button>';
                            }
                        }
                        
                    }
                    ?>
            
    </div>


                <?php
                        echo '<p id="role"> Role: ' . $row['role'] . '</p>';
                    ?>
                
                
            <div class="flex-row flex-center flex-space">
                <p class="subtitle">Real name</p>
                <?php if (isset($row['name']) && isset($row['name'])) echo '<p>'.$row['name'].' '.$row['surname'].'</p>'; ?>
            </div>


            <div class="flex-row flex-center flex-space">
                <p class="subtitle">Country</p>
                <?php if (isset($row['country'])) echo '<p>'.$row['country'].'</p>'; ?>
            </div>

            <div class="flex-row flex-center flex-space">
                <p class="subtitle">Email</p>
                <?php if (isset($row['email'])) echo '<p>'.$row['email'].'</p>'; ?>
            </div>

            <div class="flex-row flex-center flex-space">
                <p class="subtitle">Birthday</p>
                <?php if (isset($row['birthdate'])) echo '<p>'.$row['birthdate'].'</p>'; ?>
            </div>

            <div></div>
                    <div></div>

                    <div class="list-div">
                        <p class="subtitle">Followed genres</p>
                        <?php
                        $sql = "SELECT genre_name FROM accounts_genres WHERE account_ID='$account_id'";
                        if ($result = $conn->query($sql)) {
                            while ($genre = $result->fetch_array(MYSQLI_ASSOC)) {
                                if ($genre['genre_name'] != '')
                                    echo '<p>- '.$genre['genre_name'].'</p>';
                            }
                        }
                        ?>
                    </div>

                    <div class="list-div">
                        <p class="subtitle">Languages</p>
                        <?php
                        $sql = "SELECT language_name FROM accounts_languages WHERE account_ID='$account_id'";
                        if ($result = $conn->query($sql)) {
                            while ($language = $result->fetch_array(MYSQLI_ASSOC)) {
                                if ($language['language_name'] != '')
                                    echo '<p>- '.$language['language_name'].'</p>';
                            }
                        }
                        ?>
                    </div>

                    <div class="flex-row">

                    </div>


                    <div class="flex-row">
                        <?php
                        if ($row['role'] == 'moderator') {
                            echo '<div> Moderator of:';
                            $sql = "SELECT language_name FROM moderators_languages WHERE moderator_ID='$account_id'";
                            if ($result = $conn->query($sql)) {
                                while ($language = $result->fetch_array(MYSQLI_ASSOC)) {
                                    if ($language['language_name'] != '')
                                        echo '<div language_moderator_tag" value=' . $language['language_name'] . '>
                        ' . $language['language_name'] . '</div>
                                ';
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
        </div>
          
        </body>

</html>