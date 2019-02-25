<?php
	if (!$_GET['id'] && !$_GET['item']) { die("No ID nummer or Name of the item found..<br/>Example:<br/><b>Vectra-bot.org/parser/item.php?item=ITEM<b><br/>Vectra-bot.org/parser/item.php?id=NR<br/><br/></b></b>Info taken from: <b>Zybez.net ~ Item Database</b><br/>~ Vectra staff \n"); }	
		if (!$_GET['id']) {
			if (!$_GET['item']) { die("Enter an item to search for, <br />EX: Vectra-bot.org/parser/item.php?item=ITEM \n"); }	
				$source = file_get_contents("http://www.zybez.net/items.php?search_area=name&search_term=".str_replace(" ","+",$_GET["item"]));
			if (strpos($source,"no items match your search criteria")) { die("Item is not found.\n"); }
			if (strpos($source,"Examine:")) { 
			preg_match_all("/Members:<\/td><td>(.*?)<\/td><\/tr><tr><td>Tradable:<\/td><td>(.*?)<\/td>/i",$source,$members);
			preg_match_all("/<td>Equipable:<\/td><td>(.*?)<\/td><\/tr><tr><td>Stackable:<\/td><td>(.*?)<\/td>/i",$source,$stack);
			preg_match_all("/Weight:<\/td><td>(.*?)<\/td><\/tr><tr><td valign=(.*?)>Quest:<\/td><td>(.*?)<\/td>/i",$source,$weight);
			preg_match_all("/Examine:<\/td><td>(.*?)<\/td>/i",$source,$examine);
			preg_match_all("/Market Price:<\/td><td width(.*?)name(.*?) id(.*?)>(.*?)<\/div>/i",$source,$markprice);
			preg_match_all("/High Alchemy:<\/td><td>(.*?)<\/td><\/tr><tr><td>Low Alchemy:<\/td><td>(.*?)<\/td>/i",$source,$alch);
			preg_match_all("/to General Store:<\/td><td>(.*?)<\/td><\/tr><tr><td>Buy from General Store:<\/td><td>(.*?)<\/td>/i",$source,$gen);
			preg_match_all("/Attack Bonuses<(.*?)>Stab: (.*?)<br \/>Slash: (.*?)<br \/>Crush: (.*?)<br \/>Magic: (.*?)<br \/>Range: (.*?)<\/td>/i",$source,$att);
			preg_match_all("/Defence Bonuses<(.*?)>Stab: (.*?)<br \/>Slash: (.*?)<br \/>Crush: (.*?)<br \/>Magic: (.*?)<br \/>Range: (.*?)<br \/>Summoning: (.*?)<\/td>/i",$source,$def);
			preg_match_all("/Other Bonuses<(.*?)>Strength: (.*?)<br \/>Prayer: (.*?)<\/td>/i",$source,$other);
			preg_match_all("/Obtained From: <\/td><td>(.*?)<\/td>/i",$source,$obtain);
			preg_match_all("/Notes:<\/td><td>(.*?)<\/td>/i",$source,$notes);
						preg_match_all("/<title>(.*?) - Runescape Item Database - Zybez Runescape Help<\/title>/i",$source,$name);
			echo "<vectra> \n" ;
			echo "Name: ".$name[1][0].": \n" ;
		echo "Members: ".$members[1][0].": \n" ;
		echo "High Alchemy: ".$alch[1][0].": \n" ;
		echo "Low Alchemy: ".$alch[2][0].": \n" ;
		}
			if (strpos($source,"Browsing")) { echo "<vectra> \n" ;
			 preg_match_all("/><\/a><\/td><td class(.*?)><(.*?)>(.*?)<\/a><\/td>/i",$source,$name);
		 preg_match_all("/<td class(.*?)><a href(.*?)id=(.*?)&amp(.*?)><img src/i",$source,$itemid);
		 preg_match_all("/<td align=(.*?) width=(.*?)>Browsing (.*?) Items(.*?)<\/td>/i",$source,$results);
		 if ($ID = "1") { echo "Results Found: ".$results[3][0].": \n" ; }
		 			for ($ID = "0"; $ID < "15"; $ID++) {
		   if (!$name[1][$ID]) { echo "</vectra>\n" ;
		   break; }
			echo $name[3][$ID].": #".$itemid[3][$ID].": \n";
						}	
					} 
				}
			if (!$_GET['item']) {  
			
		$source = file_get_contents("http://www.zybez.net/items.php?id=".str_replace(" ","+",$_GET["id"]));
		if (strpos($source,"no items match your search criteria")) { die("Item is not found.\n"); }
				preg_match_all("/Members:<\/td><td>(.*?)<\/td><\/tr><tr><td>Tradable:<\/td><td>(.*?)<\/td>/i",$source,$members);
			preg_match_all("/<td>Equipable:<\/td><td>(.*?)<\/td><\/tr><tr><td>Stackable:<\/td><td>(.*?)<\/td>/i",$source,$stack);
			preg_match_all("/Weight:<\/td><td>(.*?)<\/td><\/tr><tr><td valign=(.*?)>Quest:<\/td><td>(.*?)<\/td>/i",$source,$weight);
			preg_match_all("/Examine:<\/td><td>(.*?)<\/td>/i",$source,$examine);
			preg_match_all("/Market Price:<\/td><td(.*?)name(.*?) id(.*?)>(.*?)<\/div>/i",$source,$markprice);
			preg_match_all("/High Alchemy:<\/td><td>(.*?)<\/td><\/tr><tr><td>Low Alchemy:<\/td><td>(.*?)<\/td>/i",$source,$alch);
			preg_match_all("/to General Store:<\/td><td>(.*?)<\/td><\/tr><tr><td>Buy from General Store:<\/td><td>(.*?)<\/td>/i",$source,$gen);
			preg_match_all("/Attack Bonuses<(.*?)>Stab: (.*?)<br \/>Slash: (.*?)<br \/>Crush: (.*?)<br \/>Magic: (.*?)<br \/>Range: (.*?)<\/td>/i",$source,$att);
			preg_match_all("/Defence Bonuses<(.*?)>Stab: (.*?)<br \/>Slash: (.*?)<br \/>Crush: (.*?)<br \/>Magic: (.*?)<br \/>Range: (.*?)<br \/>Summoning: (.*?)<\/td>/i",$source,$def);
			preg_match_all("/Other Bonuses<(.*?)>Strength: (.*?)<br \/>Prayer: (.*?)<\/td>/i",$source,$other);
			preg_match_all("/Obtained From: <\/td><td>(.*?)<\/td>/i",$source,$obtain);
			preg_match_all("/Notes:<\/td><td>(.*?)<\/td>/i",$source,$notes);
						preg_match_all("/<title>(.*?) - Runescape Item Database - Zybez Runescape Help<\/title>/i",$source,$name);
			echo "<vectra> \n" ;
			echo "Name: ".$name[1][0].": \n" ;
		echo "Members: ".$members[1][0].": \n" ;
		echo "High Alchemy: ".$alch[1][0].": \n" ;
		echo "Low Alchemy: ".$alch[2][0].": \n" ;
	}
?>