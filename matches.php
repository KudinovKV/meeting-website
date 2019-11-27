<html>
<?php
    include_once "header.php";
?>

<link rel="stylesheet" type="text/css" href="css/mathes.css">


<div class ="contentMy">

    <div class="matches">

            <form action="matches.php" method="post" autocomplete="off">
            <!-- login -->
            <div class="searchTextView">
            <label class="logintext">Search</label><br>
            <input type="text" name="substring" placeholder=<?php if (isset($_POST['substring'])) echo $_POST['substring']; else echo "'Input text for search'";?> required autofocus><br>
            </div>
            <input name="apiandfunctionname" type="hidden" value="apiphoto_matchessearch"/>
            <!-- requester type -->
            <input name="version" type="hidden" value="brawser"/>
            <!-- submit -->         
            <button class="btn waves-effect waves-purple" type="submit" name="submit">Search<i class="material-icons right">search</i></button>
            </form>
    <?php
    
    // Create request in likes db
    $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
    $result = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "'");
    $GotMatch = 0;
    if($result->num_rows == 0) : ?>
        
    <script>
    	alert('Sorry, no matches for you..')
    	window.location.href = "index.php";
    </script>
        
        
    <?php else : ?>
    
        <?php 
        // All matches loop
		$count = 0;
        while($partnerid = $result->fetch_assoc()) :
            // Check the reverse answer (find matches)
            $matches_result = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $partnerid['id2'] . "' AND id2 = '" . $_SESSION['UID'] . "'"); 
			$count++;
        ?>

            

            <?php if ($matches_result->num_rows == 0) : ?>
            <?php elseif ($matches_result->num_rows == 1) : 
				
				
                $partnerinfo = $mysql->query("SELECT * FROM info WHERE id = '" . $partnerid['id2'] . "'");
                $partner = $partnerinfo->fetch_assoc();
                // Check the time
 				$myrecord = $matches_result->fetch_assoc();
                $mytime = $myrecord['time'];
				$partnertime = $partnerid['time'];
				$time = $mytime;
				if ($partnertime > $time)
					$time = $partnertime;
				if(time() > $time)
				{
					// Delete
					$mysql->query("DELETE FROM likes WHERE id1 = '" . $partnerid['id2'] . "' AND id2 = '" . $_SESSION['UID'] . "' ");
					$mysql->query("DELETE FROM likes WHERE id1 = '" . $_SESSION['UID'] . "' AND id2 = '" . $partnerid['id2'] . "' ");
					continue;
				}
				
				// This is match
				$GotMatch++;
				// Show user info
				$path = 'images/' . $partnerid['id2'] . '/';
                $images = scandir($path);
                $images = preg_grep('/\\.(?:png|gif|jpe?g)$/', $images);
                $image = array_shift($images);

                $filename = $path . $image ;
                $handle = fopen($filename, "rb"); 
                $contents = fread($handle,filesize($filename)); 
                fclose($handle); 
                $base64content = base64_encode($contents);
                $src = 'data:image/jpeg;base64,'. $base64content; 

            ?>
                <?php if (!isset($_POST['apiandfunctionname'])||(($_POST['apiandfunctionname']=="apiphoto_matchessearch")
                &&((!(FALSE===strpos($partner['firstname'],$_POST['substring']))||!(FALSE===strpos($partner['lastname'],$_POST['substring'])))))): ?>
                    <div class="onematch">
                        <img class="matchimages" src="<?=$src?>">
                        <div class="matchtext">
                            <h3 class="matchheading"><?= $partner['firstname'] . ' ' . $partner['lastname'] ?></h3>
                            <p class="matchdescription"><?= $partner['aboutyou'] ?></p>
                        </div>
                        <div class="matchtextinfo">
                            <h3 class="matchheading"><?= $partner['privatinfo'] ?></h3>
                        </div>
                    </div>
                    
                
                <?php endif;?>

            <?php endif;?>

        <?php endwhile; ?>
    
		

    <?php endif; ?>
        
    <?php if ($GotMatch == 0) : ?>
    <script>
    	alert('Sorry, no matches for you..')
    	window.location.href = "index.php";
    </script>
    <?php endif; ?>
	
    </div>
</div>

<?php 
    include_once "footer.php"; 
?>


<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="javascript/materialize.js"></script>
</html>