<?php
    session_start();
	unset($_SESSION['UID']);
	unset($_SESSION['LOGIN']);
	unset($_SESSION['GENDER']);
	session_write_close();
	header("location: login.php");
?>