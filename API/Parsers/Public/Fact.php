<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else {
		switch (strtolower($_GET['q'])) {
			case "vin":
			case "mrt":
			case "chuck":
				$_Link = array(
								"http://4q.cc",
								"/index.php?pid=fact&person=". strtolower($_GET['q']),
								True
								);
				break;
			default:
				$_Link = array(
								"http://www.randomfunfacts.com",
								'/',
								False
								);
				break;
		}
		$cOpts 		= array(
							CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_FRESH_CONNECT => 1
							);
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], '', 120, $cOpts, 0);
		if (!is_object($src)) {
			if ($_Link[2] == True)
				preg_match('@<div id="factbox">\s*(.+)</div>@i', $src, $out);
			else
				preg_match('@<strong><i>\s*(.+)\s*</i></strong>@i', $src, $out);
			print "FACT: {$out[1]}\n";
		} else {
			cacheErrorHandler($src);
		}
	}
?>