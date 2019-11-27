<?php

class apiauth extends apiBaseClass {
	function login ($params)
	{
		$login    = $params['login'];
		$password = $params['password'];
		$retJSON = $this->createDefaultJson();
		session_start();
		if (empty($_SESSION['CSRFTOCKEN'])) {
		    $_SESSION['CSRFTOCKEN'] = bin2hex(random_bytes(32));
		}
		$token = $_SESSION['CSRFTOCKEN'];
		$_SESSION['REMOTEADDR'] = $_SERVER['REMOTE_ADDR'];
		$retJSON->csrftocken = $token;
	
		$login = filter_var(trim($login) , FILTER_SANITIZE_STRING);
		$password = filter_var(trim($password) , FILTER_SANITIZE_STRING);
		// проверка имени пользователя
		$errflag = false;
		if($login == '') 
		{
			$_SESSION['ERROR_MESSAGE'] = 'Uncorrect username(valid length is 3-30 characters)';
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result="Uncorrect password(valid length is 3-30 characters)";
				return $retJSON;
			}
			header("Location: login.php");
			exit();
		}
		// проверка пароля
		if($password == '') 
		{
			$_SESSION['ERROR_MESSAGE'] = 'Uncorrect password(valid length is 3-30 characters)';
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result="Uncorrect password(valid length is 3-30 characters)";
				return $retJSON;
			}
			header("Location: login.php");
			exit();
		}

		// запрос к базе данных
		$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');

		if($stmt = mysqli_prepare($mysql, "SELECT id, login, gender, salt , password FROM info WHERE login = ? "))
		{
			mysqli_stmt_bind_param($stmt, "s", $login);
			mysqli_stmt_execute($stmt);
			$result   = $stmt->get_result();
			mysqli_stmt_close($stmt);
			if ($result->num_rows==0) 
			{ 
				$_SESSION['ERROR_MESSAGE'] = "No users with this pair login/password"; 
				if ($params['version']=="mobile") 
				{ 
					$retJSON->result ="Failed, user didnt found"; 
					return $retJSON; 
				}
				header("location: ../login.php"); 
				exit(); 
			}
			$finduser = $result->fetch_assoc();

			$hashpassword = hash("sha512" , $password . $finduser['salt']);
			if(!hash_equals($finduser['password'],$hashpassword))
			{
				$_SESSION['ERROR_MESSAGE'] = "No users with this pair login/password"; 
				if ($params['version']=="mobile") 
				{ 
					$retJSON->result ="Failed, user didnt found"; 
					return $retJSON; 
				}
				header("location: ../login.php"); 
				exit(); 
			}
			$mysql->close();
			$_SESSION['UID'] = $finduser['id']; 
			$_SESSION['LOGIN'] = $finduser['login'];
			$_SESSION['GENDER'] = $finduser['gender'];
		}
		else
		{
			$_SESSION['ERROR_MESSAGE'] = "Technical problems (mysql query error)."; 
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result="Failed, technical problems (mysql query error).";
				return $retJSON;
			}
			header("location: ../login.php"); 
			exit();
		}

		if ($params['version'] == "mobile")
		{
			$retJSON->result="OK";
			session_write_close();

			return $retJSON;
		}
		$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
		$qry = $mysql->query("SELECT * FROM likes WHERE id2 = '".$_SESSION['UID']."'");
		$numLikes = mysqli_num_rows($qry);
		mysqli_close($mysql);
		$_SESSION['numlikes'] = $numLikes;

		session_write_close();

		header("location: ../member.php");
		exit();
	}

	function registration ($params)
	{

		$retJSON = $this->createDefaultJson();
		session_start();
		if ($params['version'] != "mobile")
		{
		    if (!isset($params['submit']))
		    {
		        $_SESSION['ERROR_MESSAGE'] = 'Please login correctly again';
				session_write_close();
				header("Location: ../registration.php");
				exit();
		    }
	    }


	    /*=========================================== Load user info ===========================================*/

	    // Составляем и отправляем запрос к бд
		$login = filter_var(trim($params['login']) , FILTER_SANITIZE_STRING);
		$password = filter_var(trim($params['password']) , FILTER_SANITIZE_STRING);
		$firstname = filter_var(trim($params['firstname']) , FILTER_SANITIZE_STRING);
		$lastname = filter_var(trim($params['lastname']) , FILTER_SANITIZE_STRING);
		$privateinfo = filter_var(trim($params['privateinfo']) , FILTER_SANITIZE_STRING);
		
		if ($params['version'] != "mobile")
		{
			$aboutyou = filter_var(trim($params['aboutyou']) , FILTER_SANITIZE_STRING);
		}
		// Проверки длины
		if(mb_strlen($login) < 3 || mb_strlen($login) > 30)
		{
			$_SESSION['ERROR_MESSAGE'] = 'Uncorrect login(valid length is 3-30 characters)';
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result= 'Uncorrect login(valid length is 3-30 characters)';
				return $retJSON;
			}
			header("Location: ../registration.php");
			exit();
		}
		if(mb_strlen($password) < 3 || mb_strlen($password) > 55)
		{
			$_SESSION['ERROR_MESSAGE'] = 'Uncorrect password(valid length is 3-55 characters)';
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result='Uncorrect password(valid length is 3-55 characters)';
				return $retJSON;
			}
			header("Location: ../registration.php");
			exit();
		}
		if(mb_strlen($privateinfo) < 3 || mb_strlen($privateinfo) > 55)
		{
			$_SESSION['ERROR_MESSAGE'] = 'Uncorrect privateinfo(valid length is 3-55 characters)';
			session_write_close();
			if ($params['version'] == "mobile")
			{
				$retJSON->result='Uncorrect privateinfo(valid length is 3-55 characters)';
				return $retJSON;
			}
			header("Location: ../registration.php");
			exit();
		}
		// Generate salt
		$bytes = random_bytes(20);
		$salt = bin2hex($bytes);
		$password = hash("sha512" , $password . $salt);
		$gender = 1;
		if ($params['gender'] == 'Woman')
			$gender = 0;

		$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
		if ($params['version']=='mobile')
		{
			$stmt = mysqli_prepare($mysql, "INSERT INTO info VALUES(0, ?, ?, ?, ?,'2000-01-01', ?, 'Not filled yet', ? , ?)");
		}
		else
		{
			$stmt = mysqli_prepare($mysql, "INSERT INTO info VALUES(0, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		}
	    if ($stmt)
	    {
	    	if ($params['version']=='mobile')
			{
	        	mysqli_stmt_bind_param($stmt, "sssssss", $login, $password, $firstname, $lastname, $gender , $privateinfo , $salt);
	    	}
	        else
	        {
	            mysqli_stmt_bind_param($stmt, "sssssssss", $login, $password, $firstname, $lastname, $params['birthday'], $gender , $aboutyou, $privateinfo , $salt);
	        }
	        mysqli_stmt_execute($stmt);
	        $result = $stmt->get_result();
	        mysqli_stmt_close($stmt);

	    	if ($mysql->error)
	    	{
	    		$_SESSION['ERROR_MESSAGE'] = 'Error then create account: '. $mysql->error;
	    		$mysql->close();
	    		session_write_close();
				if ($params['version'] == "mobile")
				{
					$retJSON->result='Error then create account: '. $mysql->error;
					return $retJSON;
				}
	    		header("Location: ../registration.php");
	    		exit();
	    	}
	    }

	    /*=========================================== Load user images ===========================================*/

	    $id = $mysql->insert_id;
	    $mysql->close();
	    // Создаем папку, если нужно
	    if(!is_dir("../images/".$id))
	      mkdir("../images/".$id, 0755, true);

	    // Изменим структуру $_FILES
	    if (isset($_FILES['profileimage']))
	    {
		    foreach($_FILES['profileimage'] as $key => $value)
		    {
		        foreach($value as $k => $v) {
		            $_FILES['profileimage'][$k][$key] = $v;
		        }
		        // Удалим старые ключи
		        unset($_FILES['profileimage'][$key]);
		    }
		    // Загружаем все картинки по порядку
		    foreach ($_FILES['profileimage'] as $k => $v)
		    {
		        // Загружаем по одному файлу
		        $filePath = $_FILES['profileimage'][$k]['tmp_name'];
		        $errorCode = $_FILES['profileimage'][$k]['error'];
		        // Проверим на ошибки
		        if ($errorCode !== UPLOAD_ERR_OK || !is_uploaded_file($filePath))
		        {
		            // Массив с названиями ошибок
		            $errorMessages = [
		                UPLOAD_ERR_INI_SIZE   => 'Размер файла превысил значение upload_max_filesize в конфигурации PHP.',
		                UPLOAD_ERR_FORM_SIZE  => 'Размер загружаемого файла превысил значение MAX_FILE_SIZE в HTML-форме.',
		                UPLOAD_ERR_PARTIAL    => 'Загружаемый файл был получен только частично.',
		                UPLOAD_ERR_NO_FILE    => 'Файл не был загружен.',
		                UPLOAD_ERR_NO_TMP_DIR => 'Отсутствует временная папка.',
		                UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
		                UPLOAD_ERR_EXTENSION  => 'PHP-расширение остановило загрузку файла.',
		            ];
		            // Зададим неизвестную ошибку
		            $unknownMessage = 'An unknown error occurred while downloading the file';
		            // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
		            $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
		            // Выведем название ошибки
		            $_SESSION['ERROR_MESSAGE'] = $outputMessage;
		            session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result=$outputMessage;
						return $retJSON;
					}
		            header("Location: ../registration.php");
		            exit();
		        }
		        // Создадим ресурс FileInfo
		        $fi = finfo_open(FILEINFO_MIME_TYPE);
		        // Получим MIME-тип
		        $mime = (string) finfo_file($fi, $filePath);
		        // Проверим ключевое слово image (image/jpeg, image/png и т. д.)
		        if (strpos($mime, 'image') === false)
		        {
		            $_SESSION['ERROR_MESSAGE'] = 'Only images can be uploaded';
		            session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result= 'Only images can be uploaded';
						return $retJSON;
					}
		            header("Location: ../registration.php");
		            exit();
		        }
		        // Результат функции запишем в переменную
		        $image = getimagesize($filePath);
		        // Зададим ограничения для картинок
		        $limitBytes  = 1024 * 1024 * 5;
		        $limitWidth  = 3840;
		        $limitHeight = 2160;
		        // Проверим нужные параметры
		        if (filesize($filePath) > $limitBytes)
		        {
		            $_SESSION['ERROR_MESSAGE'] = 'Image size must not exceed 5 MB';
		            session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result = 'Image size must not exceed 5 MB';
						return $retJSON;
					}
		            header("Location: ../registration.php");
		            exit();
		        }
		        if ($image[1] > $limitHeight)
		        {
		            $_SESSION['ERROR_MESSAGE'] = 'Image height should not exceed 1080 pixels';
		            session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result= 'Image height should not exceed 1080 pixels';
						return $retJSON;
					}
		            header("Location: ../registration.php");
		            exit();
		        }
		        if ($image[0] > $limitWidth)
		        {
		            $_SESSION['ERROR_MESSAGE'] = 'Image width should not exceed 1920 pixels';
		            session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result = 'Image width should not exceed 1920 pixels';
						return $retJSON;
					}
		            header("Location: ../registration.php");
		            exit();
		        }
		        // Сгенерируем новое имя файла на основе SHA512-хеша
		        $name = hash_file( "sha512" , $filePath);
		        // Сгенерируем расширение файла на основе типа картинки
		        $extension = image_type_to_extension($image[2]);
		        // Сократим .jpeg до .jpg
		        $format = str_replace('jpeg', 'jpg', $extension);
		        // Переместим картинку с новым именем и расширением в папку
		        if (!move_uploaded_file($filePath, '../images/' . $id . '/' . $name . $format))
		        {
		            $_SESSION['ERROR_MESSAGE'] = 'An error occurred while writing the image to disk';
				    session_write_close();
					if ($params['version'] == "mobile")
					{
						$retJSON->result= 'An error occurred while writing the image to disk';
						return $retJSON;
					}
					header("Location: ../registration.php");
					exit();
		        }
		    }
		}
	    session_write_close();
		if ($params['version'] == "mobile")
		{
		    $retJSON->result = 'OK';
		    return $retJSON;
		}
	    header("Location: ../login.php");
	    exit();
	}
	function getcsrftocken ($params)
	{
		$retJSON = $this->createDefaultJson();
		session_start(); 
		$retJSON->csrftocken = $_SESSION['CSRFTOCKEN'];
	    session_write_close();
	    $retJSON->result="OK";
	    return $retJSON;
	}
	function logout($params)
	{
		$retJSON = $this->createDefaultJson();
		session_start(); 
		$_SESSION['UID']    = 0; 
		$_SESSION['LOGIN']  = 0;
		$_SESSION['GENDER'] = 0;
	    session_write_close();
	    $retJSON->result="OK";
	    return $retJSON;
	}

}