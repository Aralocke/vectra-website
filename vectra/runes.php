<?php	
		$source = file_get_contents("http://www.zybez.net/priceguide.php?search_terms=".str_replace(" ","+",runes)."&search_area=5&price_low=&price_high=&member=1");
		preg_match_all("/<td>(.*) rune<\/td>/i",$source,$rune);
		preg_match_all("/<td>(.*)gp - (.*)gp<\/td>/i",$source,$gp);
		preg_match_all("/<td>(.*)gp<\/td>/i",$source,$gp1);
		
			for ($ID = "0"; $ID < "21"; $ID++) {
   if (!$rune[1][$ID]) { break; }
   if (!$gp1[1][$ID]) { echo $rune[1][$ID]." rune,".$gp[1][$ID]."gp - ".$gp[2][$ID]."gp:\n"; }
   if ($gp1[1][$ID]) { echo $rune[1][$ID]." rune,".$gp1[1][$ID]."gp:\n"; }
}	
?>