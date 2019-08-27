<?php
    if(empty($_POST['ACTION']))
    {
        session_start();
        $_SESSION['ERROR_MESSAGE'] = 'Error: empty action';
        session_write_close();
        header("Location: profile.php");
        exit();
    }
    
    if($_POST['ACTION'] == 'GETID')
    {
        session_start();
        /*============================ Get all users id ============================*/
        $ids_array = array();
        // Find another gender partner
        $another_gender = 0;
        if($_SESSION['GENDER'] == 0)
            $another_gender = 1;
        
        $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
        $result = $mysql->query("SELECT id FROM info WHERE gender = '$another_gender'");
        while($row = $result->fetch_array())
        {
            $ids_array[] = $row['id'];
        }
        /*============================ Find users who did not like ============================*/
        $ids_toshow = array();
        foreach ($ids_array as $currentid)
        {
            if ($currentid == $_SESSION['UID'])
            {
                // Don't show ourselves
                continue;
            }
            $like_result = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "' AND id2 = '$currentid'");
            if ($like_result->num_rows == 1)
            {
                // Already saw
                continue;
            }
            else if ($like_result->num_rows == 0)
            {
                // Add to show
                $ids_toshow[] = $currentid;
            }
        }
        /*============================ Choose a random user to show ============================*/
        if(empty($ids_toshow))
        {
            echo 'Nothing to show. Please come back later...';
        }
        else
        {
            $idusertoshow = $ids_toshow[array_rand($ids_toshow, 1)];
            echo $idusertoshow;
        }
        session_write_close();
        exit();
    }
    else if($_POST['ACTION'] == 'GETPATHS')
    {
        $idusertoshow = $_POST['ID'];
        // Get profile images
        $path = 'images/' . $idusertoshow . '/';
        $images = scandir($path);
        $images = preg_grep('/\\.(?:png|gif|jpe?g)$/', $images);
        echo json_encode($images);
    }
    else if($_POST['ACTION'] == 'SETSWIPE')
    {
        $action = $_POST['SAFESWIPE'];
        $partnerid = $_POST['CURRENTID'];
        if($action == 'LIKE')
        {
            session_start();
            $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
            $result = $mysql->query("INSERT INTO likes VALUES(0, '". $_SESSION['UID']. "','$partnerid')");
            if ($mysql->error)
            {
                session_write_close();
                echo 'Error then safe like : ' . $mysql->error;
                $mysql->close();
                exit();
            }
            
            $infopartner = $mysql->query("SELECT * FROM info WHERE id = '".$partnerid."' ");
            if ($mysql->error)
            {
                session_write_close();
                echo 'Error then safe like : ' . $mysql->error;
                $mysql->close();
                exit();
            }
            
            session_write_close();
            $mysql->close();

            echo $infopartner['firstname'] . ' ' . $infopartner['lastname'];
            exit();
        }
        else if($action == 'DISLIKE'){
            echo 'Correct';
            exit();
        }
        else{
            echo 'Error: uncorrect action';
            exit();
        }
    }
    else{
        echo 'Error: uncorrect action';
        exit();
    }
?>