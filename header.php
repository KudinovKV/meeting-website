<head>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="http://localhost:8000/KeepUp/css/style.css">
	<link rel="stylesheet" type="text/css" href="http://localhost:8000/KeepUp/css/materialize.css">
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
      } 
      session_write_close();
  ?>
</head>
<div class="header">
	<nav role="navigation">
	<div class="nav-wrapper container"><a id="logo-container" href="http://localhost:8000/KeepUp/member.php" class="brand-logo">Keep up</a>
    <ul class="right hide-on-med-and-down">	
      <li><a href="http://localhost:8000/KeepUp/index.php">Home</a></li>
      <?php if (isset($_SESSION['UID'])): ?>
        <!-- User is registered -->
        <li><a href="http://localhost:8000/KeepUp/matches.php">My matches</a></li>
        <li><a href="http://localhost:8000/KeepUp/profile.php">My profile</a></li>
        <li><a href="http://localhost:8000/KeepUp/auth/logout.php">Logout</a></li>
      <?php else : ?>
        <!-- User not registered -->
        <li><a href="http://localhost:8000/KeepUp/auth/registration.php">Registration</a></li>
        <li><a href="http://localhost:8000/KeepUp/auth/login.php">Login</a></li>
      <?php endif ; ?>
    </ul>
    <ul id="nav-mobile" class="sidenav">
      <li><a href="http://localhost:8000/KeepUp/index.php">Home</a></li>
      <?php if (isset($_SESSION['UID'])) : ?>
        <li><a href="http://localhost:8000/KeepUp/matches.php">My matches</a></li>
        <li><a href="http://localhost:8000/KeepUp/profile.php">My profile</a></li>
        <li><a href="http://localhost:8000/KeepUp/auth/logout.php">Logout</a></li>
      <?php else : ?>
        <li><a href="http://localhost:8000/KeepUp/auth/registration.php">Registration</a></li>
        <li><a href="http://localhost:8000/KeepUp/auth/login.php">Login</a></li>
      <?php endif ; ?>
    </ul>
    <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
  </div>
  </nav>
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="http://localhost:8000/KeepUp/javascript/materialize.js"></script>
</div>

