<html>

<?php 
	include_once "header.php" ; 
?>

<div class="contentMy">
	<div class="loginform">
		<form enctype="multipart/form-data" action="api/index.php"  method="post" autocomplete="off"> <br>
		<!-- login -->
		<label class="logintext">Login: </label> <br>
		<input type = "text" name="login" placeholder="Input your login" required autofocus> <br>
		<!-- password -->
		<label class="logintext">Password: </label> <br>
		<input type = "password" name="password" placeholder="123456789" id="password" required> <br>
		<!-- confirm password -->
		<label class="logintext">Confirm password: </label> <br>
		<input type = "password" name="confirm password" placeholder="123456789" id="confirm_password" required> <br>
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
		<p><label><input name="gender" id="gender0" type="radio" checked value='Men' /><span>Men</span></label></p>
		<p><label><input name="gender" id="gender1" type="radio" value='Woman'/><span>Woman</span></label></p>
		<!-- aboutyou -->
		<input type = "text" name="aboutyou" placeholder="Something about you" required><br><br>
		<!-- privateinfo -->
		<label class="logintext">Something to contact with you: </label> <br>
		<input type = "text" name="privateinfo" placeholder="+78005553535" required><br><br>
		<!-- profileimage -->
		<div class="btn waves-effect ">
			<label for="upload-photo" class="upload">Browse photo...</label>
			<input type="file" name="profileimage[]" id="upload-photo" class="upload-photo" accept=".jpg, .jpeg, .png" multiple>
		</div><br><br>
		<!-- api function name -->
        <input name="apiandfunctionname" type="hidden" value="apiauth_registration"/>
        <!-- requester type -->
        <input name="version" type="hidden" value="brawser"/>
		<!-- button -->
		<button class="btn waves-effect" type="submit" name="submit">Submit<i class="material-icons right">send</i></button>
		<!-- clear -->
		<button type="reset" class="btn waves-effect">Reset all</button>
		</form>
	</div>
</div>
<?php 
	include_once "footer.php";
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
<script type="text/javascript" src="javascript/upload-photo.js"></script>
<script type="text/javascript" src="javascript/confirm_password.js"></script>
</html>