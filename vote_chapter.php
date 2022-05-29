<?php
    require('conn.php');
    session_start();

    if($_POST){
        if($_SESSION){
            $chapter_ID = $conn->real_escape_string($_POST['chapter_id']);
            $input = $conn->real_escape_string($_POST['vote']);
            $account_ID = $_SESSION['id'];
            $sql = "SELECT vote FROM votes_chapters where votes_chapters.chapter_ID = '$chapter_ID' AND votes_chapters.account_ID = '$account_ID'";        
            if($result = $conn->query($sql)){
                $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if(is_bool($input)){
                    $input = intval($input);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sql1 = NULL;
                    $sql2 = NULL;
                    if($row){
                        $sql1 = "DELETE FROM votes_chapters WHERE votes_chapters.chapter_ID = '$chapter_ID' AND votes_chapters.account_ID = '$account_ID'";
                        if($row['vote'] == true){
                            $sql2 = "UPDATE chapters SET total_votes = total_votes -1 WHERE chapter_ID = '$chapter_ID'";
                            echo -1;
                        }
                        else{
                            $sql2 = "UPDATE chapters SET total_votes = total_votes +1 WHERE chapter_ID = '$chapter_ID'";
                            echo +1;
                        }
                    }
                    else{
                        $sql1 = "INSERT INTO votes_chapters (account_ID, chapter_ID, vote) VALUES('$account_ID', '$chapter_ID', '$input')";
                        if($input == true){
                            $sql2 = "UPDATE chapters SET total_votes = total_votes +1 WHERE chapter_ID = '$chapter_ID'";
                            echo +1;
                        }
                        else{
                            $sql2 = "UPDATE chapters SET total_votes = total_votes -1 WHERE chapter_ID = '$chapter_ID'";
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