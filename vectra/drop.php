<?php
	if (!$_GET['id'] && !$_GET['monster']) { die("No ID nummer or Name of the monster found..<br/>Example:<br/><b>Vectra-bot.org/parser/monster.php?monster=MONSTER<b><br/>Vectra-bot.org/parser/monster.php?id=NR<br/><br/></b></b>Info taken from: <b>Zybez.net ~ Database</b><br/>~ Vectra \n"); }	
		if (!$_GET['id']) {	
			if (!$_GET['monster']) { die("Enter an item to search for, <br />EX: Vectra-bot.org/parser/monster.php?monster=MONSTER \n"); }	
				$source = file_get_contents("http://www.zybez.net/monsters.php?search_area=name&search_term=".str_replace(" ","+",$_GET["monster"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found.\n"); }
				if (strpos($source,"Browsing")) { 
				preg_match_all("/<td class=(.*)>(.*?)<\/td><td class=(.*)>(.*)<\/td><td class=(.*)>(.*)<\/td><\/tr>/i",$source,$combat);
							 preg_match_all("/<td class=(.*)<\/td>(.*)<a href(.*)id=(.*?)&amp(.*?)>(.*?)<\/a><\/td>/i",$source,$itemid);
							 preg_match_all("/<td align=(.*?) width=(.*?)>Browsing (.*?) monsters(.*?)<\/td>/i",$source,$results);
				       if ($ID = "1") { echo "Results Found: ".$results[3][0].": \n" ; }
				 			for ($ID = "0"; $ID < "15"; $ID++) { if (!$itemid[1][$ID]) { break; }
					       echo $itemid[6][$ID]."(".$combat[2][$ID]."): ID: #".$itemid[4][$ID]." \n"; } 
						   } 
						} 						   
		if (!$_GET['monster']) { $source = file_get_contents("http://www.zybez.net/monsters.php?id=".str_replace("#","",$_GET["id"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found on Zybez.net. <br> Vectra \n"); }
		preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
			preg_match_all("/Drops:<\/td><td>(.*?)<\/a><\/td><\/tr><tr>/i",$source,$drop);
			 if ($ID = "1") { echo "Name: ".$name[1][0]."\n" ; }
   if ($drop[1][0]==null) { $x = NA; }
	else { $x = $drop[1][0]; }
	echo ":".strip_tags($drop[1][0]).":\n";  
	}	
	
		if ($_GET['monster']) { if (!strpos($source,"Browsing")) { $source = file_get_contents("http://www.zybez.net/monsters.php?search_area=name&search_term=".str_replace(" ","+",$_GET["monster"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found on Zybez.net. <br> Vectra \n"); }
		preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
			preg_match_all("/Drops:<\/td><td>(.*?)<\/a><\/td><\/tr><tr>/i",$source,$drop);
			 if ($ID = "1") { echo "Name: ".$name[1][0]."\n" ; }
   if ($drop[1][0]==null) { $x[1][0] = NA; }
	else { $x[1][0] = $drop[1][0]; }
	echo ":".strip_tags($drop[1][0]).":\n"; 
	}	
	}
?>