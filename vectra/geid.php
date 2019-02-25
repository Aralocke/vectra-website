<?php
$connection = mysql_connect();
mysql_select_db("botinfo", $connection);
	if (isset($_GET['ge'])) {   
		$getinfo1 = "SELECT channel, network FROM blacklist";
				$getinfo1q = mysql_query($getinfo1) or die(mysql_error());
				while ($getinforow1 = mysql_fetch_assoc($getinfo1q)) {
					if ($_GET['ge'] == $getinforow1['channel']) {
						if ($_GET['id'] == $getinforow1['network']) {
							die("Already got that one, thanks.");						
						}
					}
				}
		$getid1 = "SELECT max(id)+1 id FROM blacklist";
		$getid1q = mysql_fetch_assoc(mysql_query($getid1));
		$add2ge = "INSERT INTO blacklist (id, channel, network) 	VALUES ('$getid1q[id]','$_GET[ge]','$_GET[id]')";
		$add2geq = mysql_query($add2ge) or die(mysql_error());
		echo "The Item: $_GET[ge] - $_GET[id] added to the list. $getid1q[id] ";
	}
			if(isset($_GET['item'])) {
					$getinfo2 = "SELECT channel, network FROM blacklist";
		$getinfo2q = mysql_query($getinfo2) or die(mysql_error());
					while ($getinforow2 = mysql_fetch_assoc($getinfo2q)) {
			if ($getinforow2['channel'] == $_GET['item']) {
					die("found! ID:".$getinforow2['network'].":end \n");
			}
			else { die("Not Found \n");
          }			
		}
	}
?>