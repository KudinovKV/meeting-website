<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-147147538-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-147147538-1');
</script>

 <?php header("Content-Security-Policy: default-src http://localhost/ http://localhost/KeepUp 'self' 'unsafe-eval' https://www.google-analytics.com/analytics.js https://code.jquery.com/ https://www.googletagmanager.com/gtag/ https://fonts.googleapis.com/ http://cdn.jsdelivr.net/ https://fonts.gstatic.com/ 'unsafe-inline' http://cdn.jsdelivr.net/emojione; img-src http://localhost/KeepUp/ https://www.google-analytics.com/ 'self' data:;");?>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="http://localhost/KeepUp/css/style.css">
	<link rel="stylesheet" type="text/css" href="http://localhost/KeepUp/css/materialize.css">
  <link rel="stylesheet" href="http://cdn.jsdelivr.net/emojione/1.3.0/assets/css/emojione.min.css"/>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <?php 
      session_start();
      if(isset($_SESSION['UID']) && isset($_SESSION['LOGIN'])) 
      {
          $UID = $_SESSION['UID'];
          $login = $_SESSION['LOGIN'];
          $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
          $result = $mysql->query("SELECT * FROM info WHERE id = '".$UID."' AND login = '".$login."'");
          if($result->num_rows != 1)
          {
            print_r("FIND MORE THEN 1 USER OR NOBOBY"); 
            unset($_SESSION['UID']);
            unset($_SESSION['LOGIN']);
            unset($_SESSION['GENDER']);
          }
          mysqli_close($mysql);
      } 
      session_write_close();
  ?>
</head>
<div class="header">
	<nav role="navigation">
	<div class="nav-wrapper container"><a id="logo-container" href="member.php" class="brand-logo">Keep up</a>
    <ul class="right hide-on-med-and-down">	
      <li><a href="index.php">Home</a></li>
      <?php if (isset($_SESSION['UID'])): ?>
        <!-- User is registered -->
        <li><a href="matches.php">My matches</a></li>
        <li><a href="profile.php">My profile</a></li>
        <li><a href="changeprofile.php">Change profile</a></li>

        <li><a href="logout.php">Logout</a></li>
      <?php else : ?>
        <!-- User not registered -->
        <li><a href="registration.php">Registration</a></li>
        <li><a href="login.php">Login</a></li>
      <?php endif ; ?>
    </ul>
    <ul id="nav-mobile" class="sidenav">
      <li><a href="index.php">Home</a></li>
      <?php if (isset($_SESSION['UID'])) : ?>
        <li><a href="matches.php">My matches</a></li>
        <li><a href="profile.php">My profile</a></li>
        <li><a href="changeprofile.php">Change profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      <?php else : ?>
        <li><a href="registration.php">Registration</a></li>
        <li><a href="login.php">Login</a></li>
      <?php endif ; ?>
    </ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
  </div>
  </nav>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://localhost/KeepUp/javascript/materialize.js"></script>
  <script src="http://localhost/KeepUp/javascript/init.js"></script>
</div>

