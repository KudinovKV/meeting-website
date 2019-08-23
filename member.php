<?php
    session_start();

    if(!isset($_SESSION['UID'])) 
	{
        header("Location: index.php");
        exit();
    }
    if (!isset($_SESSION['LOGIN']))
    {
        header("Location: index.php");
        exit();
    }
    if (!isset($_SESSION['GENDER']))
    {
        header("Location: index.php");
        exit();
    }
    
    session_write_close();

    include_once "header.php";
?>

<div class="contentMy">
    <?php 
        session_start();
        print_r($_SESSION) ; 
        print_r(session_id());
        session_write_close();
    ?>
</div>

<?php
    include_once "footer.php";
?>
