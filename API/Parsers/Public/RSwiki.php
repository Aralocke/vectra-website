<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else if (empty($_GET['q'])) {
		print "ERROR: Missing argument `q`\n";
	} else {
		$q 			= urlencode($_GET['q']);
		$_Link 		= array("http://runescape.wikia.com", "/api.php?action=query&prop=info&inprop=url&format=json&titles={$q}");
		$cOpts 		= array(
						CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1
					  );
		$bad		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
		$cacheFile	= '';
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
		if (!is_object($src)) {
			$src = json_decode($src, true);
			$src = $src['query']['pages'];
			foreach ($src as $key => $val) break;
			if ($key == "-1") {
				print "ERROR: Non-existant article.\n";
			} else {
				$q 			= $src[$key]['title'];
				$url 		= $src[$key]['fullurl'];
				$__link 	= explode('/', $url);
				$_Link[1] 		= '';
				for ($i = 3; $i < count($__link); $i++) { $_Link[1] .= "/{$__link[$i]}"; }
				unset($__link, $url);
				print "ARTICLE: {$q}\nURL: ". ((SHORT_LINKS) ? Google::shortUrl($_Link[0].$_Link[1]):$_Link[0].$_Link[1]) . "\n";
				$cacheFile 	= "RSwiki." . str_replace($bad, '_', $q);
				$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
				if (!is_object($src)) {
					preg_match('@<meta name="description" content="([^"]+)" />@i', $src, $desc);
					print "DESC: {$desc[1]}\n";
				} else {
					cacheErrorHandler($src);
				}
			}
		} else {
			cacheErrorHandler($src);
		}
	}
?>