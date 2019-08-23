<?php
    session_start();

    if (!isset($_POST['submit']))
    {
        $_SESSION['ERROR_MESSAGE'] = 'Please login correctly again';
		session_write_close();
		header("Location: registration.php");
		exit();
    }


    /*=========================================== Load user info ===========================================*/

    // Составляем и отправляем запрос к бд
	$login = filter_var(trim($_POST['login']) , FILTER_SANITIZE_STRING);
	$password = filter_var(trim($_POST['password']) , FILTER_SANITIZE_STRING);
	$firstname = filter_var(trim($_POST['firstname']) , FILTER_SANITIZE_STRING);
	$lastname = filter_var(trim($_POST['lastname']) , FILTER_SANITIZE_STRING);
	$aboutyou = filter_var(trim($_POST['aboutyou']) , FILTER_SANITIZE_STRING);

	// Проверки длины
	if(mb_strlen($login) < 3 || mb_strlen($login) > 30)
	{
		$_SESSION['ERROR_MESSAGE'] = 'Uncorrect login(valid length is 3-30 characters)';
		session_write_close();
		header("Location: registration.php");
		exit();
	}
	if(mb_strlen($password) < 3 || mb_strlen($password) > 30)
	{
		$_SESSION['ERROR_MESSAGE'] = 'Uncorrect password(valid length is 3-30 characters)';
		session_write_close();
		header("Location: registration.php");
		exit();
	}


	$password = md5($password."ashkdhasdhkhad212312");
	$gender = 1;
	if ($_POST['gender'] == 'Women')
		$gender = 0;

	$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
	$mysql->query("INSERT INTO info VALUES(0 , '$login','$password','$firstname','$lastname','".$_POST['birthday']."','$gender','$aboutyou')");

	if ($mysql->error)
	{
		$_SESSION['ERROR_MESSAGE'] = 'Error then create account: '. $mysql->error;
		$mysql->close();
		session_write_close();
		header("Location: registration.php");
		exit();
	}


    /*=========================================== Load user images ===========================================*/

    $id = $mysql->insert_id;
    $mysql->close();
    // Создаем папку, если нужно
    if(!is_dir("../images/".$id))
      mkdir("../images/".$id, 0755, true);

    // Изменим структуру $_FILES
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
            header("Location: registration.php");
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
            header("Location: registration.php");
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
            header("Location: registration.php");
            exit();
        }
        if ($image[1] > $limitHeight)
        {
            $_SESSION['ERROR_MESSAGE'] = 'Image height should not exceed 1080 pixels';
            session_write_close();
            header("Location: registration.php");
            exit();
        }
        if ($image[0] > $limitWidth)
        {
            $_SESSION['ERROR_MESSAGE'] = 'Image width should not exceed 1920 pixels';
            session_write_close();
            header("Location: registration.php");
            exit();
        }
        // Сгенерируем новое имя файла на основе MD5-хеша
        $name = md5_file($filePath);
        // Сгенерируем расширение файла на основе типа картинки
        $extension = image_type_to_extension($image[2]);
        // Сократим .jpeg до .jpg
        $format = str_replace('jpeg', 'jpg', $extension);
        // Переместим картинку с новым именем и расширением в папку
        if (!move_uploaded_file($filePath, '../images/' . $id . '/' . $name . $format))
        {
            $_SESSION['ERROR_MESSAGE'] = 'An error occurred while writing the image to disk';
		    session_write_close();
		    header("Location: registration.php");
		    exit();
        }
    }
    session_write_close();
    header("Location: login.php");
?>
