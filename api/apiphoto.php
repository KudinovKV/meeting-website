<?php

class apiphoto extends apiBaseClass
{
	function getphoto($params)
	{
		session_start();
		$retJSON = $this->createDefaultJson();
		if ($_SERVER['REMOTE_ADDR'] != $_SESSION['REMOTEADDR'])
		{
			$retJSON->result = "Failed";
			$_SESSION['UID'] = 0;
			$_SESSION['LOGIN']  = 0;
			$_SESSION['GENDER'] = 0;
	    	session_write_close();
	    	return $retJSON;
		}
		if (isset($params['csrftocken']))
		{
			 if (hash_equals($_SESSION['CSRFTOCKEN'],$params['csrftocken'] ))
			  {
			  	$retJSON->result="OK";
			  }
			  else
			  {
			  	$retJSON->result="Failed";
			  	return $retJSON;
			  }
         // Proceed to process the form data
		}
		else
		{
			$retJSON->result="Failed";
			return $retJSON;
		}
		if (isset($_SESSION['UID']) && $_SESSION['UID'] != 0)
		{
	        $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
	        $qry       = mysqli_query($mysql, "SELECT * FROM info");
	        $numPeople = mysqli_num_rows($qry);
	     	if (!isset($params['getprofile'])||$params['getprofile'] == 0)
	     	{
		        do
		        {
			        $anotherGender = $_SESSION['GENDER']^1;
			        $qry = mysqli_query($mysql, "SELECT * FROM info WHERE gender = '".$anotherGender."'");
	                $numPeopleAnotherGender = mysqli_num_rows($qry);
					if ($numPeopleAnotherGender == 0)
					{
						$retJSON->result = "Failed";
					    session_write_close();
						return $retJSON;
					}
                    $genId = (rand() % $numPeople) + 1;
                    $_SESSION['GENID'] = $genId;
                    $qry = mysqli_query($mysql, "SELECT * FROM info WHERE id = '".$genId."'");
                    $row = mysqli_fetch_assoc($qry);
					$gender = (int)$row['gender'];

				}
				while(($_SESSION['GENDER'] == $gender)||($_SESSION['UID']==$genId));
			}
			else if ($params['getprofile'] == 1)
			{
				$qry = mysqli_query($mysql, "SELECT * FROM info WHERE id = '".$_SESSION['UID']."'");
			    $row = mysqli_fetch_assoc($qry);
			    $_SESSION['GENID'] = $_SESSION['UID'];
			}
			else if ($params['getprofile'] == 2)
			{
		       	$qry = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "' AND id2 = '".$params['selectid']."'");
		       	if ($qry->num_rows == 1)
		        {
			        $qry = $mysql->query("SELECT * FROM likes WHERE id1 = '" .$params['selectid']. "' AND id2 = '".$_SESSION['UID']."'");
			        if ($qry->num_rows == 1)
			        {
			        	$qry = mysqli_query($mysql, "SELECT * FROM info WHERE id = '".$params['selectid']."'");
			        	$_SESSION['GENID'] = $params['selectid'];
			        	$row = mysqli_fetch_assoc($qry);
		        		$retJSON->result="OK";
			        }
		        }
		        else
		        {
		        	$retJSON->result="Failed";
		        	return $retJSON;
		        }
		       

			}

			$retJSON->firstname = $row['firstname'];
			$retJSON->lastname  = $row['lastname'];
			$retJSON->birthday  = $row['birthday'];
			$retJSON->aboutyou  = $row['aboutyou'];
			$retJSON->genid     = $_SESSION['GENID'];
			$path   = '../images/' . $_SESSION['GENID'] . '/';
			$images = scandir($path);
			$images = preg_grep('/\\.(?:png|jpe?g)$/', $images);
	        $countPhoto = count($images);
	        $retJSON->numPhoto = $countPhoto;
	        $i = 0;
	        $imagesArr = array();
	        foreach($images as $image)
	        {
				$filename = "../images/".$_SESSION['GENID'].'/'.htmlspecialchars(urlencode($image));
				$handle = fopen($filename, "rb");
				$contents = fread($handle,filesize($filename));
				fclose($handle);
				$base64content = base64_encode($contents);
				array_push($imagesArr,$base64content);
			}
			$retJSON->images = $imagesArr;
			$retJSON->result = "OK";
		}
		else
		{
			$retJSON->result = "Failed";
		}
		session_write_close();
		return $retJSON;
	}

	function getprofile($params)
	{
		$params['getprofile'] = 1;
		$retJSON = $this->getphoto($params);
		return $retJSON;
	}
	function getprofilepartner($params)
	{
		$params['getprofile'] = 2;
		$retJSON = $this->getphoto($params);
		return $retJSON;
	}

	function like($params)
	{
		session_start();
		if ($_SERVER['REMOTE_ADDR'] != $_SESSION['REMOTEADDR'])
		{
			$retJSON->result = "Failed";
			$_SESSION['UID'] = 0;
			$_SESSION['LOGIN']  = 0;
			$_SESSION['GENDER'] = 0;
	    	session_write_close();
	    	return $retJSON;
		}
		$retJSON = $this->createDefaultJson();
		if (isset($params['csrftocken']))
		{
			 if (hash_equals($_SESSION['CSRFTOCKEN'], $params['csrftocken']))
			  {
			  	$retJSON->result="OK";
			  }
			  else
			  {
			  	$retJSON->result="Failed";
			  	return $retJSON;
			  }
         // Proceed to process the form data
		}
		else
		{
			$retJSON->result="Failed";
			return $retJSON;
		}
		if ($params['genid'] != $_SESSION["GENID"])
		{
			$retJSON->result = "Failed";
			session_write_close();
			return $retJSON;
		}
        $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
       	$likeResult = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "' AND id2 = '".$_SESSION['GENID']."'");
       	if ($likeResult->num_rows == 0)
        {
			$endoftime = time() + random_int(8, 10) * 60 * 60; // 8 - 10 hours
            $mysql->query("INSERT INTO likes VALUES(0, '". $_SESSION['UID']. "','".$_SESSION['GENID']."' , '".$endoftime."')");
        }
        $retJSON->result="OK";
        $likeResult = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['GENID'] . "' AND id2 = '".$_SESSION['UID']."'");
        if ($likeResult->num_rows == 1)
        {
        	$retJSON->result="MATCH";
        }
        session_write_close();
        return $retJSON;	
	}
	function getmatches($params)
	{
		session_start();
		if ($_SERVER['REMOTE_ADDR'] != $_SESSION['REMOTEADDR'])
		{
			$retJSON->result = "Failed";
			$_SESSION['UID'] = 0;
			$_SESSION['LOGIN']  = 0;
			$_SESSION['GENDER'] = 0;
	    	session_write_close();
	    	return $retJSON;
		}
		$retJSON = $this->createDefaultJson();
		if (isset($params['csrftocken']))
		{
			 if (hash_equals($_SESSION['CSRFTOCKEN'], $params['csrftocken']))
			  {
			  	$retJSON->result="OK";
			  }
			  else
			  {
			  	$retJSON->result="Failed";
			  	return $retJSON;
			  }
         // Proceed to process the form data
		}
		else
		{
			$retJSON->result="Failed";
			return $retJSON;
		}


		$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');


		$userLikes = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "'");
		$matchArray = array();
		while($partner = mysqli_fetch_assoc($userLikes))
		{
			$matches = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $partner['id2'] . "' AND id2 = '".$_SESSION['UID']."'");
			while ($matchWithUser = mysqli_fetch_assoc($matches))
			{
				$qry = $mysql->query( "SELECT * FROM info WHERE id = '".$matchWithUser['id1']."'");
				$matchInfo = mysqli_fetch_assoc($qry);


				$mytime = $matchWithUser['time'];
				$partnertime = $partner['time'];
				$time = $mytime;

				if ($partnertime > $time)
					$time = $partnertime;

				if(time() > $time)
				{
					// Delete
					$mysql->query("DELETE FROM likes WHERE id1 = '" . $partner['id2'] . "' AND id2 = '" . $_SESSION['UID'] . "' ");
					$mysql->query("DELETE FROM likes WHERE id1 = '" . $_SESSION['UID'] . "' AND id2 = '" . $partner['id2'] . "' ");
					continue;
				}
				/*get photo*/
				else
				{
					$path   = '../images/' . $matchInfo['id'] . '/';
					$images = scandir($path);
					$images = preg_grep('/\\.(?:png|jpe?g)$/', $images);
			        $countPhoto = count($images);
			        foreach($images as $image)
			        {
						$filename = "../images/".$matchInfo['id'].'/'.htmlspecialchars(urlencode($image));
						$handle = fopen($filename, "rb");
						$contents = fread($handle,filesize($filename));
						fclose($handle);
						$base64content = base64_encode($contents);
						$matchInfo['image'] = $base64content;
						break;
					}
					array_push($matchArray, $matchInfo);
			}
			}
		}
		$retJSON->result = "OK";
		$retJSON->matches = $matchArray;
		session_write_close();
		return $retJSON;
	}
}