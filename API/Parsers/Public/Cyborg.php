<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else {
		if (empty($_GET['q'])) {
			print "ERROR: Missing argument `q`\n";
		} else {
			$bad		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
			$_Link 		= array(
								"http://cyborg.namedecoder.com",
								"/?acronym=" . urlencode($_GET['q'])
								);
			$q 			= str_replace($bad, '_', $_GET['q']);
			$cOpts 		= array(
								CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_RETURNTRANSFER => 1,
								CURLOPT_FRESH_CONNECT => 1
								);
			$cacheFile 	= "Cyborg." . $q;
			$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
			if (!is_object($src)) {
				if (preg_match('@<p class="mediumheader">([^:]+): (.+)</p>@i', $src, $match)) {
					print "CYBORG: {$match[1]}\n";
					print "DESCRIPTION: {$match[2]}\n";
				} else {
					print "ERROR: Could not formulate.\n";
				}
			} else {
				cacheErrorHandler($src);
			}
		}
	}
?>