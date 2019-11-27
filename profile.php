
<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>


<?php
    include_once 'header.php';
    session_start();
?>

<?php
    // Get info about user
    $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
    $result = $mysql->query("SELECT * FROM info WHERE id = '".$_SESSION['UID']."' ");
    $user = $result->fetch_assoc();
    // If cant find user
    if (count($user) == 0)
    {
        $_SESSION['ERROR_MESSAGE'] = 'Please log in again';
        session_write_close();
        header("Location: logout.php");
        exit();
    }
?>

<div class = "contentMy">
    <!-- Profile photos -->
    <div class="profileimages">
        <?php
            // Get progile images
            $path = 'images/' . $_SESSION['UID'] . '/';
            $images = scandir($path);
            $images = preg_grep('/\\.(?:png|gif|jpe?g)$/', $images);
            $index = 0;
            foreach($images as $image):
                $index++;
        ?>
        <div class="slide">
          <div class="slideimage">
            <img src="<?= $path . $image?>" alt="Profile image">
          </div>
          <div class="slidetext">
            <h3 class="slideheading"><?= $user['firstname'] . ' '. $user['lastname'] ?></h3>
            <p class="slidedescription"><?= $user['aboutyou'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>


<?php
    include_once "footer.php";
?>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="slick/slick.min.js"></script>
<script type="text/javascript" src="javascript/profile.js"></script>