<html>

<?php include_once "header.php";?>
<div class="contentMy">
	<div class="loginform">
		<form action="api/index.php" method="post" autocomplete="off">
			<!-- login -->
			<label class="logintext">Login:</label><br>
			<input type="text" name="login" placeholder="Input your login" required autofocus><br>
			<!-- password -->
			<label class="logintext">Password:</label><br>
			<input type="password" name="password" placeholder="123456789" required ><br><br>
			<!-- api function name -->
            <input name="apiandfunctionname" type="hidden" value="apiauth_login"/>
            <!-- requester type -->
            <input name="version" type="hidden" value="brawser"/>
			<!-- submit -->			
			<button class="btn waves-effect waves-purple" type="submit" name="submit" onsubmit="ga('send' , 'pageview' , 'AuthClick')">Submit<i class="material-icons right">send</i></button>
		</form>
		<!-- registation -->
		<button onclick="window.location='registration.php'" class = "btn waves-effect" id="logsub">Registration</button>		
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
</html>