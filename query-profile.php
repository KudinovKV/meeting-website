<?php

if (!isset($_POST['submit']))
{
    $_SESSION['ERROR_MESSAGE'] = 'Please login correctly again';
    session_write_close();
    header("Location: profile.php");
    exit();
}

session_start();
/*=========================================== Load user info ===========================================*/

// Create request to db
$firstname = filter_var(trim($_POST['firstname']) , FILTER_SANITIZE_STRING);
$lastname = filter_var(trim($_POST['lastname']) , FILTER_SANITIZE_STRING);
$aboutyou = filter_var(trim($_POST['aboutyou']) , FILTER_SANITIZE_STRING);
// Connect with db
$mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
// firstname
if ($stmt = mysqli_prepare($mysql, "UPDATE info SET firstname=? WHERE id=?"))
{
  mysqli_stmt_bind_param($stmt, "si", $firstname, $_SESSION['UID']);
  mysqli_stmt_execute($stmt);
  $result = $stmt->get_result();
  mysqli_stmt_close($stmt);
  if ($mysql->error)
  {
    $_SESSION['ERROR_MESSAGE'] = 'Error then update account: '. $mysql->error;
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }
}
// lastname
if ($stmt = mysqli_prepare($mysql, "UPDATE info SET lastname=? WHERE id=?"))
{
  mysqli_stmt_bind_param($stmt, "si", $lastname, $_SESSION['UID']);
  mysqli_stmt_execute($stmt);
  $result = $stmt->get_result();
  mysqli_stmt_close($stmt);
  if ($mysql->error)
  {
    $_SESSION['ERROR_MESSAGE'] = 'Error then update account: '. $mysql->error;
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }
}
// birthday
if ($stmt = mysqli_prepare($mysql, "UPDATE info SET birthday=? WHERE id=?"))
{
  mysqli_stmt_bind_param($stmt, "si", $_POST['birthday'], $_SESSION['UID']);
  mysqli_stmt_execute($stmt);
  $result = $stmt->get_result();
  mysqli_stmt_close($stmt);
  if ($mysql->error)
  {
    $_SESSION['ERROR_MESSAGE'] = 'Error then update account: '. $mysql->error;
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }
}
// gender
$gender = 1;
if ($_POST['gender'] == 'Woman')
    $gender = 0;
$_SESSION['GENDER'] = $gender;
if ($stmt = mysqli_prepare($mysql, "UPDATE info SET gender=? WHERE id=?"))
{
  mysqli_stmt_bind_param($stmt, "ii", $gender, $_SESSION['UID']);
  mysqli_stmt_execute($stmt);
  $result = $stmt->get_result();
  mysqli_stmt_close($stmt);
  $mysql->query("UPDATE info SET gender='".$gender."' WHERE id='".$_SESSION['UID']."'");
  if ($mysql->error)
  {
    $_SESSION['ERROR_MESSAGE'] = 'Error then update account: '. $mysql->error;
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }
}
// aboutyou
if ($stmt = mysqli_prepare($mysql, "UPDATE info SET aboutyou=? WHERE id=?"))
{
  mysqli_stmt_bind_param($stmt, "si", $aboutyou, $_SESSION['UID']);
  mysqli_stmt_execute($stmt);
  $result = $stmt->get_result();
  mysqli_stmt_close($stmt);
  if ($mysql->error)
  {
    $_SESSION['ERROR_MESSAGE'] = 'Error then update account: '. $mysql->error;
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }
}
$mysql->close();




/*=========================================== Clear all past photo ===========================================*/

// Изменим структуру $_FILES
foreach($_FILES['profileimage'] as $key => $value)
{
    foreach($value as $k => $v) {
        $_FILES['profileimage'][$k][$key] = $v;
    }
    // Удалим старые ключи
    unset($_FILES['profileimage'][$key]);
}


if($_POST['deletepastphoto'] == 'yes')
{
  if(count($_FILES['profileimage']) == 1 && $_FILES['profileimage'][0]['error'] == UPLOAD_ERR_NO_FILE)
  {
    $_SESSION['ERROR_MESSAGE'] = 'You cannot delete all old photos and not upload new ones';
    $mysql->close();
    session_write_close();
    header("Location: profile.php");
    exit();
  }

  $files = glob("images/". $_SESSION['UID'] ."/*");
  if (count($files) > 0)
  {
    foreach ($files as $file)
    {
      if (file_exists($file))
      {
        unlink($file);
      }
    }
  }
}

/*=========================================== Load user images ===========================================*/


if($_POST['deletepastphoto'] == 'no' && $_FILES['profileimage'][0]['error'] == UPLOAD_ERR_NO_FILE)
{
  session_write_close();
  header("Location: profile.php");
  exit();
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
            UPLOAD_ERR_INI_SIZE   => 'File too large',
            UPLOAD_ERR_FORM_SIZE  => 'File too large',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload',
        ];
        // Зададим неизвестную ошибку
        $unknownMessage = 'An unknown error occurred while downloading the file';
        // Если в массиве нет кода ошибки, скажем, что ошибка неизвестна
        $outputMessage = isset($errorMessages[$errorCode]) ? $errorMessages[$errorCode] : $unknownMessage;
        // Выведем название ошибки
        $_SESSION['ERROR_MESSAGE'] = $outputMessage;
        session_write_close();
        header("Location: profile.php");
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
        header("Location: profile.php");
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
        header("Location: profile.php");
        exit();
    }
    if ($image[1] > $limitHeight)
    {
        $_SESSION['ERROR_MESSAGE'] = 'Image height should not exceed 1080 pixels';
        session_write_close();
        header("Location: profile.php");
        exit();
    }
    if ($image[0] > $limitWidth)
    {
        $_SESSION['ERROR_MESSAGE'] = 'Image width should not exceed 1920 pixels';
        session_write_close();
        header("Location: profile.php");
        exit();
    }
    // Сгенерируем новое имя файла на основе SHA512-хеша
    $name = hash_file( "sha512" , $filePath);
    // Сгенерируем расширение файла на основе типа картинки
    $extension = image_type_to_extension($image[2]);
    // Сократим .jpeg до .jpg
    $format = str_replace('jpeg', 'jpg', $extension);
    // Переместим картинку с новым именем и расширением в папку
    if (!move_uploaded_file($filePath, 'images/' . $_SESSION['UID'] . '/' . $name . $format))
    {
        $_SESSION['ERROR_MESSAGE'] = 'An error occurred while writing the image to disk';
        session_write_close();
        header("Location: profile.php");
        exit();
    }
}
session_write_close();
header("Location: profile.php");

?>
