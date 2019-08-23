<?php
	// начало сессии для записи
	session_start(); 
	
	$login = filter_var(trim($_POST['login']) , FILTER_SANITIZE_STRING);
	$password = filter_var(trim($_POST['password']) , FILTER_SANITIZE_STRING);
	
	// проверка имени пользователя
	$errflag = false;
	if($login == '') 
	{
		$_SESSION['ERROR_MESSAGE'] = 'Uncorrect username(valid length is 3-30 characters)';
		session_write_close();
		header("Location: login.php");
		exit();
	}
	// проверка пароля
	if($password == '') 
	{
		$_SESSION['ERROR_MESSAGE'] = 'Uncorrect password(valid length is 3-30 characters)';
		session_write_close();
		header("Location: login.php");
		exit();
	}
	$password = md5($password."ashkdhasdhkhad212312");
	// запрос к базе данных
	$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
	$result = $mysql->query("SELECT * FROM info WHERE login = '".$login."' AND password = '".$password."'");
	$finduser = $result->fetch_assoc();
	//проверка, был ли запрос успешным
	
	if(count($finduser) == 0)
	{
		$_SESSION['ERROR_MESSAGE'] = "No users with this pair login/password"; 
	    session_write_close();
	    header("location: login.php"); 
	    exit();
	}
	

	$_SESSION['UID'] = $finduser['id']; 
	$_SESSION['LOGIN'] = $finduser['login'];
	$_SESSION['GENDER'] = $finduser['gender'];
	session_write_close();
	
	
	header("location: ../member.php");
?>