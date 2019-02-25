<?php
	if (!$_GET['id'] && !$_GET['monster']) { die("No ID nummer or Name of the monster found..<br/>Example:<br/><b>Vectra-bot.org/parser/monster.php?monster=MONSTER<b><br/>Vectra-bot.org/parser/monster.php?id=NR<br/><br/></b></b>Info taken from: <b>Zybez.net ~ Database</b><br/>~ Vectra \n"); }	
		if (!$_GET['id']) {	if (!$_GET['monster']) { die("Enter an item to search for, <br />EX: Vectra-bot.org/parser/monster.php?monster=MONSTER \n"); }	
				$source = file_get_contents("http://www.zybez.net/monsters.php?search_area=name&search_term=".str_replace(" ","+",$_GET["monster"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found.\n"); }
		if (strpos($source,"Browsing")) { preg_match_all("/<td class=(.*)>(.*?)<\/td><td class=(.*)>(.*)<\/td><td class=(.*)>(.*)<\/td><\/tr>/i",$source,$combat);
					 preg_match_all("/<td class=(.*)<\/td>(.*)<a href(.*)id=(.*?)&amp(.*?)>(.*?)<\/a><\/td>/i",$source,$itemid);
					 preg_match_all("/<td align=(.*?) width=(.*?)>Browsing (.*?) monsters(.*?)<\/td>/i",$source,$results);
		       if ($ID = "1") { echo "Results Found: ".$results[3][0].": \n" ; }
		 			for ($ID = "0"; $ID < "15"; $ID++) { if (!$itemid[1][$ID]) { break; }
			       echo $itemid[6][$ID]."(".$combat[2][$ID]."): ID: #".$itemid[4][$ID]." \n"; } } } 
			if (!$_GET['monster']) { $source = file_get_contents("http://www.zybez.net/monsters.php?id=".str_replace("#","",$_GET["id"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found on Zybez.net. <br> Vectra \n"); }
		if (strpos($source,"Quest:</td><td><a href=")) { preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
		    preg_match_all("/<center><a href=(.*) title=(.*)><img src=(.*) alt=(.*) border=(.*)><\/a><\/center><table cellspacing=(.*)<img src=(.*) alt=(.*)><\/td><\/tr><tr><td width=(.*)>Combat:<\/td><td width=(.*?)>(.*?)<\/td>/i",$source,$cb);
			preg_match_all("/<td>Hitpoints:<\/td><td>(.*?)<\/td>/i",$source,$hp);
			preg_match_all("/Max Hit:<\/td><td>(.*?)<\/td>/i",$source,$max);
			preg_match_all("/Race:<\/td><td>(.*?)<\/td>/i",$source,$race);
			preg_match_all("/Members:<\/td><td>(.*?)<\/td>/i",$source,$member);
			preg_match_all("/Quest:<\/td><td><a href=(.*)>(.*?)<\/a><\/td>/i",$source,$quest);
			preg_match_all("/Nature:<\/td><td>(.*?)<\/td><\/tr><tr><td>Attack Style:<\/td><td>(.*?)<\/td><\/tr><tr><td>Examine:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>(.*)>Where Found:<\/td><td>(.*?)<\/td><\/tr><tr>(.*)Notes:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>/i",$source,$x);
   if ($cb[11][0]==null) { $cmb = NA; }
	else { $cmb = $cb[11][0]; }
   if ($max[1][0]==null) { $maxhit = NA; }
	else { $maxhit = $max[1][0]; }
	   if ($race[1][0]==null) { $ra = NA; }
	else { $ra = $race[1][0]; }
   if ($hp[1][0]==null) { $hitp = NA; }
	else { $hitp = $hp[1][0]; }
   if ($member[1][0]==null) { $memb = NA; }
	else { $memb = $member[1][0]; }
   if ($quest[1][0]==null) { $quests = NA; }
	else { $quests = $quest[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[2][0]==null) { $attstyle = NA; }
	else { $attstyle = $x[2][0]; }
   if ($x[3][0]==null) { $examine = NA; }
	else { $examine = $x[3][0]; }
   if ($x[5][0]==null) { $found = NA; }
	else { $found = $x[5][0]; }
   if ($x[7][0]==null) { $notes = NA; }
	else { $notes = $x[7][0]; }
	echo "Name: ".$name[1][0].": Combat: ".$cmb.": Hitpoints: ".$hitp.": Max Hit: ".$maxhit.": Race: ".$ra.": Member: ".$memb.": Quest: ".$quests.": Nature: ".$nature.": Attack Style: ".$attstyle.": Examine: ".$examine.": Found: ".$found." : notes: ".$notes." \n"; }	
	if (!strpos($source,"Quest:</td><td><a href=")) { preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
		    preg_match_all("/<center><a href=(.*) title=(.*)><img src=(.*) alt=(.*) border=(.*)><\/a><\/center><table cellspacing=(.*)<img src=(.*) alt=(.*)><\/td><\/tr><tr><td width=(.*)>Combat:<\/td><td width=(.*?)>(.*?)<\/td>/i",$source,$cb);
			preg_match_all("/<td>Hitpoints:<\/td><td>(.*?)<\/td>/i",$source,$hp);
			preg_match_all("/Max Hit:<\/td><td>(.*?)<\/td>/i",$source,$max);
			preg_match_all("/Race:<\/td><td>(.*?)<\/td>/i",$source,$race);
			preg_match_all("/Members:<\/td><td>(.*?)<\/td>/i",$source,$member);
			preg_match_all("/Quest:<\/td><td>(.*?)<\/td>/i",$source,$quest);
			preg_match_all("/Nature:<\/td><td>(.*?)<\/td><\/tr><tr><td>Attack Style:<\/td><td>(.*?)<\/td><\/tr><tr><td>Examine:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>(.*)>Where Found:<\/td><td>(.*?)<\/td><\/tr><tr>(.*)Notes:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>/i",$source,$x);
   if ($cb[11][0]==null) { $cmb = NA; }
	else { $cmb = $cb[11][0]; }
   if ($max[1][0]==null) { $maxhit = NA; }
	else { $maxhit = $max[1][0]; }
	   if ($race[1][0]==null) { $ra = NA; }
	else { $ra = $race[1][0]; }
   if ($hp[1][0]==null) { $hitp = NA; }
	else { $hitp = $hp[1][0]; }
   if ($member[1][0]==null) { $memb = NA; }
	else { $memb = $member[1][0]; }
   if ($quest[1][0]==null) { $quests = NA; }
	else { $quests = $quest[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[2][0]==null) { $attstyle = NA; }
	else { $attstyle = $x[2][0]; }
   if ($x[3][0]==null) { $examine = NA; }
	else { $examine = $x[3][0]; }
   if ($x[5][0]==null) { $found = NA; }
	else { $found = $x[5][0]; }
   if ($x[7][0]==null) { $notes = NA; }
	else { $notes = $x[7][0]; }
	echo "Name: ".$name[1][0].": Combat: ".$cmb.": Hitpoints: ".$hitp.": Max Hit: ".$maxhit.": Race: ".$ra.": Member: ".$memb.": Quest: ".$quests.": Nature: ".$nature.": Attack Style: ".$attstyle.": Examine: ".$examine.": Found: ".$found." : notes: ".$notes." \n"; } }
		if ($_GET['monster']) { if (!strpos($source,"Browsing")) { $source = file_get_contents("http://www.zybez.net/monsters.php?search_area=name&search_term=".str_replace(" ","+",$_GET["monster"]));
		if (strpos($source,"no monsters match your search criteria")) { die("Monster is not found on Zybez.net. <br> Vectra \n"); }
		if (strpos($source,"Quest:</td><td><a href=")) { preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
		 preg_match_all("/<center><a href=(.*) title=(.*)><img src=(.*) alt=(.*) border=(.*)><\/a><\/center><table cellspacing=(.*)<img src=(.*) alt=(.*)><\/td><\/tr><tr><td width=(.*)>Combat:<\/td><td width=(.*?)>(.*?)<\/td>/i",$source,$cb);
			preg_match_all("/<td>Hitpoints:<\/td><td>(.*?)<\/td>/i",$source,$hp);
			preg_match_all("/Max Hit:<\/td><td>(.*?)<\/td>/i",$source,$max);
			preg_match_all("/Race:<\/td><td>(.*?)<\/td>/i",$source,$race);
			preg_match_all("/Members:<\/td><td>(.*?)<\/td>/i",$source,$member);
			preg_match_all("/Quest:<\/td><td><a href=(.*)>(.*?)<\/a><\/td>/i",$source,$quest);
			preg_match_all("/Nature:<\/td><td>(.*?)<\/td><\/tr><tr><td>Attack Style:<\/td><td>(.*?)<\/td><\/tr><tr><td>Examine:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>(.*)>Where Found:<\/td><td>(.*?)<\/td><\/tr><tr>(.*)Notes:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>/i",$source,$x);
   if ($cb[11][0]==null) { $cmb = NA; }
	else { $cmb = $cb[11][0]; }
   if ($max[1][0]==null) { $maxhit = NA; }
	else { $maxhit = $max[1][0]; }
	   if ($race[1][0]==null) { $ra = NA; }
	else { $ra = $race[1][0]; }
   if ($hp[1][0]==null) { $hitp = NA; }
	else { $hitp = $hp[1][0]; }
   if ($member[1][0]==null) { $memb = NA; }
	else { $memb = $member[1][0]; }
   if ($quest[1][0]==null) { $quests = NA; }
	else { $quests = $quest[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[2][0]==null) { $attstyle = NA; }
	else { $attstyle = $x[2][0]; }
   if ($x[3][0]==null) { $examine = NA; }
	else { $examine = $x[3][0]; }
   if ($x[5][0]==null) { $found = NA; }
	else { $found = $x[5][0]; }
   if ($x[7][0]==null) { $notes = NA; }
	else { $notes = $x[7][0]; }
	echo "Name: ".$name[1][0].": Combat: ".$cmb.": Hitpoints: ".$hitp.": Max Hit: ".$maxhit.": Race: ".$ra.": Member: ".$memb.": Quest: ".$quests.": Nature: ".$nature.": Attack Style: ".$attstyle.": Examine: ".$examine.": Found: ".$found." : notes: ".$notes." \n"; }  
     if (!strpos($source,"Quest:</td><td><a href=")) { preg_match_all("/<title>(.*?) - Runescape Monster Database - Zybez Runescape Help<\/title>/i",$source,$name);
		 preg_match_all("/<center><a href=(.*) title=(.*)><img src=(.*) alt=(.*) border=(.*)><\/a><\/center><table cellspacing=(.*)<img src=(.*) alt=(.*)><\/td><\/tr><tr><td width=(.*)>Combat:<\/td><td width=(.*?)>(.*?)<\/td>/i",$source,$cb);
			preg_match_all("/<td>Hitpoints:<\/td><td>(.*?)<\/td>/i",$source,$hp);
			preg_match_all("/Max Hit:<\/td><td>(.*?)<\/td>/i",$source,$max);
			preg_match_all("/Race:<\/td><td>(.*?)<\/td>/i",$source,$race);
			preg_match_all("/Members:<\/td><td>(.*?)<\/td>/i",$source,$member);
			preg_match_all("/Quest:<\/td><td>(.*?)<\/td>/i",$source,$quest);
			preg_match_all("/Nature:<\/td><td>(.*?)<\/td><\/tr><tr><td>Attack Style:<\/td><td>(.*?)<\/td><\/tr><tr><td>Examine:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>(.*)>Where Found:<\/td><td>(.*?)<\/td><\/tr><tr>(.*)Notes:<\/td><td>(.*?)<\/td><\/tr><\/table><br \/>/i",$source,$x);
   if ($cb[11][0]==null) { $cmb = NA; }
	else { $cmb = $cb[11][0]; }
   if ($max[1][0]==null) { $maxhit = NA; }
	else { $maxhit = $max[1][0]; }
	   if ($race[1][0]==null) { $ra = NA; }
	else { $ra = $race[1][0]; }
   if ($hp[1][0]==null) { $hitp = NA; }
	else { $hitp = $hp[1][0]; }
   if ($member[1][0]==null) { $memb = NA; }
	else { $memb = $member[1][0]; }
   if ($quest[1][0]==null) { $quests = NA; }
	else { $quests = $quest[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[1][0]==null) { $nature = NA; }
	else { $nature = $x[1][0]; }
   if ($x[2][0]==null) { $attstyle = NA; }
	else { $attstyle = $x[2][0]; }
   if ($x[3][0]==null) { $examine = NA; }
	else { $examine = $x[3][0]; }
   if ($x[5][0]==null) { $found = NA; }
	else { $found = $x[5][0]; }
   if ($x[7][0]==null) { $notes = NA; }
	else { $notes = $x[7][0]; }
	echo "Name: ".$name[1][0].": Combat: ".$cmb.": Hitpoints: ".$hitp.": Max Hit: ".$maxhit.": Race: ".$ra.": Member: ".$memb.": Quest: ".$quests.": Nature: ".$nature.": Attack Style: ".$attstyle.": Examine: ".$examine.": Found: ".$found." : notes: ".$notes." \n"; } } }
?>