
<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>


<?php
    include_once 'header.php';
    session_start();
?>

<?php
    // Get info about user
    $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
    $result = $mysql->query("SELECT * FROM info WHERE id = '".$UID."' ");
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
            <img src="<?= $path . $image?>" alt="Photo image">
          </div>
          <div class="slidetext">
            <h3 class="slideheading">Slide <?=$index?></h3>
            <p class="slidedescription">Description slide <?=$index?></p>
          </div>
        </div>
        <?php endforeach; ?>
    </div>
    <!-- Profile info -->
    <div class="loginform">
        <form enctype="multipart/form-data" action="query-profile.php"  method="post" autocomplete="off"> <br>
        <!-- password -->
        <label class="logintext">Password: </label> <br>
        <input type = "password" name="password" placeholder="123456789" required> <br>
        <!-- firstname -->
		    <label class="logintext">First name: </label> <br>
        <input type = "text" name="firstname" value="<?=$user["firstname"]?>" required> <br>
        <!-- lastname -->
        <label class="logintext">Last name: </label> <br>
        <input type = "text" name="lastname" value="<?=$user["lastname"]?>" required> <br>
        <!-- birthday -->
        <label class="logintext">Birthday date: </label> <br>
        <input type="date" name="birthday" value="<?=$user["birthday"]?>" required> <br>
        <!-- gender 0 - Women , 1 - Men -->
        <?php if ($user["gender"] == 0) : ?>
            <p><label><input name="gender" type="radio" value='Men' /><span>Men</span></label></p>
            <p><label><input name="gender" type="radio" checked value='Women'/><span>Women</span></label></p>
        <?php else: ?>
            <p><label><input name="gender" type="radio" checked value='Men' /><span>Men</span></label></p>
            <p><label><input name="gender" type="radio" value='Women'/><span>Women</span></label></p>
        <?php endif ?>
        <!-- aboutyou -->
        <input type = "text" name="aboutyou" value="<?=$user["aboutyou"]?>" required> <br> <br>
        <!-- delete last photo -->
        <label class="logintext">Delete all past photo: </label> <br>
        <p><label><input name="deletepastphoto" type="radio" value='yes' /><span>Yes</span></label></p>
        <p><label><input name="deletepastphoto" type="radio" checked value='no'/><span>No</span></label></p>
        <!-- profileimage -->
        <div class="btn waves-effect waves-light">
          <label for="upload-photo" class="upload">Add new photo...</label>
          <input type="file" name="profileimage[]" id="upload-photo" class="upload-photo" accept=".jpg, .jpeg, .png" multiple>
        </div> <br> <br>
        <!-- button -->
        <button class="btn waves-effect waves-light" type="submit" name="submit">Update profile<i class="material-icons right">send</i></button>
        </form>
    </div>

</div>


<?php
    include_once "footer.php";
    if (!empty($_SESSION['ERROR_MESSAGE']))
  	{
  		// Вывели и убрали сообщение
  		echo "<script>alert(\"".$_SESSION['ERROR_MESSAGE']."\");</script>";
  		unset($_SESSION['ERROR_MESSAGE']);
  	}
    session_write_close();
?>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="slick/slick.min.js"></script>
<script type="text/javascript" src="javascript/profile.js"></script>
<script type="text/javascript" src="javascript/upload-photo.js"></script>
