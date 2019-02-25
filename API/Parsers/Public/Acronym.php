<?php
	if (!defined('IN_PARSERS') OR !IN_PARSERS) {
		exit;
	} elseif (empty($_GET['q'])) {
		print "ERROR: Missing argument `q`\n";
	} else {
		$_Link 		= array("http://www.acronymdb.com", "/acronym/{$_GET['q']}");
		$cOpts 		= array(
							CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
							CURLOPT_FOLLOWLOCATION => TRUE,
							CURLOPT_RETURNTRANSFER => 1,
							CURLOPT_FRESH_CONNECT => 1
						);
		$bad 		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|',' ');
		$cacheFile 	= "Acronym." . str_replace($bad, '_', $_GET['q']);
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
		if (!is_object($src)) {
			preg_match_all('@<img\s*src="/img/link.gif" alt="Definition of acronym ([^:]+): ([^"]+)" title="Definition of acronym \1: \2"></a>\s*\2<br />by <a href="/user/[^"]+">@i', $src, $acronym);
			if (empty($acronym[1])) {
				print "ERROR: No matches found for '{$_GET['q']}'\n";
			} else {
				print "ACRONYM: {$acronym[1][0]}\n";
				for ($i=0;$i<count($acronym[1]);$i++) print "MEANING: {$acronym[2][$i]}\n";
			}
		} else {
			catchErrorHandler($src);
		}
	}
?>