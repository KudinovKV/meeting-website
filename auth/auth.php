<?php
	include_once "auth/database.php";
	
    function Destroy() {
	        unset($_SESSION['UID']);
			unset($_SESSION['LOGIN']);
			unset($_SESSION['GENDER']);
			header("location: login.php");
	}
	if(isset($_SESSION['UID']) && isset($_SESSION['LOGIN'])) 
	{
	    $UID = $_SESSION['UID'];
	    $username = $_SESSION['LOGIN'];
	    $qry = mysqli_query($connection, "SELECT * FROM info WHERE id = '".$UID."' AND name = '".$username."'");
        if(mysqli_num_rows($qry) != 1) 
        { 
	    	Destroy(); 
	    }
    } 
    else 
    { 
		Destroy(); 
	}
?>
