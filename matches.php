<html>
<?php
    include_once "header.php";
?>

<div class ="contentMy">
<div class="loginform">
<?php
    // Create request in likes db
    $mysql = new mysqli('localhost' , 'root' , '1234' , 'sitedb');
    $result = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $_SESSION['UID'] . "'");
    if($result->num_rows == 0)
    {
        // This user did not like anyone
        print_r('No matches for you dude!');
    }
    else
    {
        $allmatches = $result->fetch_assoc();
        $countmatches = $allmatches->num_rows; 
        // All matches loop
        for ($i = 0 ; $i < $countmatches; $i++ )
        {
            $match = $allmatches[$i];
            // Find id partner
            $partnerid = $match['id2'];
            // Check the reverse answer (find matches)
            $matches_result = $mysql->query("SELECT * FROM likes WHERE id1 = '" . $partnerid . "' AND id2 = '" . $_SESSION['UID'] . "'");

            if ($matches_result->num_rows == 0 )
            {
                print_r('No match with ' . $i . ' \n');
                continue;
            }
            else if ($matches_result->num_rows == 1)
            {
                // This is match
                $partnerinfo = $mysql->query("SELECT * FROM info WHERE id = '" . $partnerid . "'");
                $partner = $partnerinfo->fetch_assoc();
                print_r('\n' . $partner . '\n');
            }
            else 
            {
                // Find more then 1 answer ?!
                print_r('More then 1 match with ' . $i . ' \n');
                continue;
            }
        }
    }   
    
?>
</div>
</div>

<?php 
    include_once "footer.php"; 
?>
<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="javascript/materialize.js"></script>
<script type="text/javascript" src="javascript/courusel.js"></script>
</html>