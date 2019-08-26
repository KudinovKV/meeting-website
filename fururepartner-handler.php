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
        $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
        $result = $mysql->query("SELECT id FROM info");
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
    else{
        echo 'Error: uncorrect action';
        exit();
    }
?>