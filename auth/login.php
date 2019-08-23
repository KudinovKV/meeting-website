<html>

<?php include_once "../header.php";?>
<div class="contentMy">
	<div class="loginform">
		<form action="query-login.php" method="post" autocomplete="off">
			<!-- login -->
			<label class="logintext">Login:</label><br>
			<input type="text" name="login" placeholder="Input your login" required autofocus><br>
			<!-- password -->
			<label class="logintext">Password:</label><br>
			<input type="password" name="password" placeholder="123456789" required ><br><br>
			<!-- submit -->
			<button class="btn waves-effect waves-light" type="submit" name="submit">Submit<i class="material-icons right">send</i></button>
			<!-- registation -->
			<button onclick="window.location='http://localhost:8000/KeepUp/auth/registration.php'" class = "btn waves-effect waves-light" id="logsub">Registration</button>
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
</html>