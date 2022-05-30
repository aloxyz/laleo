<?php
    require('conn.php');
    session_start();

    if($_POST){
        if($_SESSION){
            $story_ID = $conn->real_escape_string($_POST['story_id']);
            $input = $conn->real_escape_string($_POST['vote']);
            $account_ID = $_SESSION['id'];
            $sql = "SELECT vote FROM votes_stories where votes_stories.story_ID = '$story_ID' AND votes_stories.account_ID = '$account_ID'";        
            if($result = $conn->query($sql)){
                $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if(is_bool($input)){
                    $input = intval($input);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sql1 = NULL;
                    $sql2 = NULL;
                    if($row){
                        $sql1 = "DELETE FROM votes_stories WHERE votes_stories.story_ID = '$story_ID' AND votes_stories.account_ID = '$account_ID'";
                        if($row['vote'] == true){
                            $sql2 = "UPDATE stories SET total_votes = total_votes -1 WHERE story_ID = '$story_ID'";
                            echo -1;
                        }
                        else{
                            $sql2 = "UPDATE stories SET total_votes = total_votes +1 WHERE story_ID = '$story_ID'";
                            echo +1;
                        }
                    }
                    else{
                        $sql1 = "INSERT INTO votes_stories (account_ID, story_ID, vote) VALUES('$account_ID', '$story_ID', '$input')";
                        if($input == true){
                            $sql2 = "UPDATE stories SET total_votes = total_votes +1 WHERE story_ID = '$story_ID'";
                            echo +1;
                        }
                        else{
                            $sql2 = "UPDATE stories SET total_votes = total_votes -1 WHERE story_ID = '$story_ID'";
                            echo -1;
                        }
                    }
                    $conn->begin_transaction();
                    $conn->query($sql1);
                    $conn->query($sql2);
                    $conn->commit();

                }
            }
            else{
                echo "Errore";
            }
        }
        else
            echo "Errore";        
    }
    else
        echo "Errore";

?>