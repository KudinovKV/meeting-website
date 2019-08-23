<html>

<?php 
	include_once "../header.php" ; 
?>

<div class="contentMy">
	<div class="loginform">
		<form enctype="multipart/form-data" action="query-registration.php"  method="post" autocomplete="off"> <br>
		<!-- login -->
		<label class="logintext">Login: </label> <br>
		<input type = "text" name="login" placeholder="Input your login" required autofocus> <br>
		<!-- password -->
		<label class="logintext">Password: </label> <br>
		<input type = "password" name="password" placeholder="123456789" required> <br>
		<!-- name -->
		<label class="logintext">First name: </label> <br>
		<input type = "text" name="firstname" placeholder="Input your first name" required> <br>
		<!-- lastname -->
		<label class="logintext">Last name: </label> <br>
		<input type = "text" name="lastname" placeholder="Input your last name" required> <br>
		<!-- birthday -->
		<label class="logintext">Birthday date: </label> <br>
		<input type="date" name="birthday" required> <br>
		<!-- gender -->
		<p><label><input name="gender" type="radio" checked value='Men' /><span>Men</span></label></p>
		<p><label><input name="gender" type="radio" value='Women'/><span>Women</span></label></p>
		<!-- aboutyou -->
		<input type = "text" name="aboutyou" placeholder="Something about you" required><br><br>
		<!-- profileimage -->
		<div class="btn waves-effect waves-light">
			<label for="upload-photo" class="upload">Browse photo...</label>
			<input type="file" name="profileimage[]" id="upload-photo" class="upload-photo" accept=".jpg, .jpeg, .png" multiple>
		</div><br><br>
		<!-- button -->
		<button class="btn waves-effect waves-light" type="submit" name="submit">Submit<i class="material-icons right">send</i></button>
		<!-- clear -->
		<button type="reset" class="btn waves-effect waves-light">Reset all</button>
		</form>
	</div>
</div>
<?php 
	include_once "../footer.php";
	if (!empty($_SESSION['ERROR_MESSAGE'])) 
	{ 
		// Вывели и убрали сообщение
		echo "<script>alert(\"".$_SESSION['ERROR_MESSAGE']."\");</script>";
		session_start();
		unset($_SESSION['ERROR_MESSAGE']);
		session_write_close();
	}
?>
<!-- Change upload button -->
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="http://localhost:8000/KeepUp/javascript/upload-photo.js"></script>
</html>