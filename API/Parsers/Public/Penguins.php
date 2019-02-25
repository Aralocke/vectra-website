<?php
	//DATE: MO, N - M (K Days left)
	//PENGUIN: LOCATION|OBJECT|POINTS
	if (!defined('IN_PARSERS') OR !IN_PARSERS) {
		exit;
	} else {
		$src = httpCacheSocket("GET", "http://world60pengs.com", '/', "World60Pengs", 120, NULL, 0);
		if (!is_object($src)) {
			preg_match('@<div class="contenttitle">Penguin Locations for World 60 on Runescape for (\d+)/(\d+) through \1/(\d+) </div>@', $src, $Period);
			print "DATE: {$Period[2]}/{$Period[1]}/" . date("Y") . " - {$Period[3]}/{$Period[1]}/" . date("Y") . "\n";
			preg_match_all("@<div class='penghead'>\d+\.\) <a href='[^']+'>([^<]+)</a> - (\S+(?: \S+)?) \((\d+) points?\)</div><div class='pengbody'>(.+?)</div>@i", $src, $Penguins);
			for ($i=0;$i<count($Penguins[1]);$i++) print "PENGUIN: {$Penguins[1][$i]}|{$Penguins[2][$i]}|{$Penguins[3][$i]}|{$Penguins[4][$i]}\n";
		} else {
			catchErrorHandler($src);
		}
	}
?>