<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else if (empty($_GET['link'])) {
		print "ERROR: Missing argument &link\n";
	} else {
		$id 		= explode("-",$_GET["link"]);
		$cacheFile 	= "Zybez.{$id[0]}" ;
		$_Link 		= $_GET["link"];
		$cOpts 		= array(
						CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1
					  );
		$src 		= httpCacheSocket("GET", "http://forums.zybez.net", "/topic/{$_Link}/", $cacheFile, 120, $cOpts, 0);
		if (!is_object($src)) {
			if (strpos($src,'<h2>An Error Occurred</h2>')) { 
			print "ERROR: Your search for \"$_Link\" did not return a valid thread.\n";
			}
			else { 
			$start 		= strpos($src, "<title>") + 7;
			$title 		= substr($src, $start, strpos($src, "</title>", $start) - $start);
			$title 		= str_replace(array("\r\n", "\n", "\r"), ' ', $title);
			$zybez[0] 	= html_entity_decode(str_replace(" - Runescape Community","",$title));
			if (preg_match("@<li class='closed'>@i", $src)) {
				$zybez[4] = "1";
			} else { 
				$zybez[4] = "0";
			} if (preg_match("@<li class='total'>\((\d+) Pages\)</li>|Page \d+ of (\d+)</span>@i", $src, $pcount)) {
				if (count(explode(' ', $pcount[0])) == 4) {
					$zybez[1] = "{$pcount[2]}";
				} else {
					$zybez[1] = "{$pcount[1]}" ;
				} 
			} if (preg_match("@Posted <abbr class=\"published\" title=\"\d+-\d+-\d+T\d+:\d+:\d+(\+\d+:\d+)\">([^<]+)</abbr>@i", $src, $post)) {
				$zybez[2] = "{$post[2]} ({$post[1]})" ; 
			} if (preg_match("@<link rel=\'author\' href=\'http\:\/\/forums\.zybez\.net\/user\/(\d+)-(\S+)/(.*)\' \/>@i", $src, $u)) {
				$zybez[3] = "{$u[2]}" ; 
				$zybez[3] = str_replace("-"," ",$zybez[3]);
			}
			if (!isset($zybez[2])) {
			print "ERROR: Your search for \"$_Link\" did not return a valid thread.\n";
			}
			else { 
			$a = array("TITLE","PAGES","PUBLISHED","AUTHOR","LOCKED");
			$x = count($a);
			for ($y = 0; $y < $x; $y++) {
				print "{$a[$y]}: {$zybez[$y]}\n";
			}
			}
			}
		} else {
			cacheErrorHandler($src);
		}
	}
?>
