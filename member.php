
<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
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
  <div class="futurepartner" id="main">
  </div>
</div>

<?php
    include_once "footer.php";
?>

<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="slick/slick.min.js"></script>
<script src="javascript/drawProfilePhotos.js"></script>