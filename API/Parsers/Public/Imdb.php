<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else if (empty($_GET['q'])) {
		print "ERROR: Missing argument `q`\n";
	} else {
		$q			= array($_GET['q'], urlencode($_GET['q']));
		$_Link 		= array("http://www.imdb.com", "/find?s=tt&q={$q[1]}");
		$cOpts 		= array(
						CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1
					  );
		$titleMatch = (preg_match("@^tt\d+$@", $q[0])) ? TRUE:FALSE;
		$bad 		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
		$cacheFile 	= ($titleMatch == TRUE) ? "IMDB.{$q[0]}":"IMDB." . str_replace($bad, '_', $q[0]);
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
		$match 		= array();
		if (!is_object($src)) {
			if (strrpos($src, '<b>No Matches.</b>')) {
				print "ERROR: No matches found\n";
			} else {
				if ($titleMatch == TRUE OR strrpos($src, '<h3>Box Office</h3>')) {
					preg_match('@<h1 class="header">\s*(.+)\s*<span>(.+)</span>@i', $src, $match[]);
					preg_match('@(?:<img width="\d+" alt="([^"]+)" src="http://i.media-imdb.com/images/\S+/certificates/us/\S+.png" class="absmiddle" title="[^"]+" height="\d+">&nbsp;&nbsp;)?(.+?)&nbsp;&nbsp;-&nbsp;@i', $src, $match[]);
					preg_match_all('@<a\s+onclick="[^"]+"\s+href="/genre/([^"]+)"\s+>@i', $src, $match[]);
					preg_match('@<span class="rating-rating">([^<]+)<span>/10</span>@i', $src, $match[]);
					preg_match('@<h4 class="inline">\s+Director:\s+</h4>\s+<a\s+href="/name/nm\d+/">([^<]+)</a></div>@i', $src, $match[]);
					preg_match('@<div class="rating rating-big" data-auth="[^"]+" id="((.)\2\d+)\|@i', $src, $match[]);
					$genre = implode(", ", $match[2][1]);
                    $Link = 'http://www.imdb.com/title/'.$match[5][1].'/' ;
					print "LINK: ".((SHORT_LINKS) ? Google::shortUrl($Link) : $Link)."\n";
					print "TITLE: {$match[0][1]}\n";
					print "YEAR: " . str_replace(array('(', ')', "TV ", "Video Game "),'',strip_tags($match[0][2])) . "\n";
					if (!empty($match[1][1])) print "RATING: {$match[1][1]}\n";
					print "LENGTH: {$match[1][2]}\nGENRE: {$genre}\nURATING: {$match[3][1]}/10\n";
					if (isset($match[4][1])) print "DIRECTOR: {$match[4][1]}\n";
				} else {
					preg_match_all('@<a href="/title/(tt\d+)/" onclick="[^"]+">([^<]+)</a>\s*\((\d+)\)\s*@i', $src, $match[]);
					$result 	= count($match[0][2]);
					$_result 	= $result-1;
					$titles 	= array();
					$num 		= (isset($_GET['num'])) ? $_GET['num'] : 1;
					$i			= $num+1;
					$m 			= $i+5;
					print "RESULTS: {$_result}\n";
					for (; $i < $m; $i++) {
						if ($i == $result) {
							$i = 1;
							$m = 6-count($titles);
						}
						$titles[] = str_replace(' ', '_', $match[0][2][$i]) . "|{$match[0][3][$i]}|{$match[0][1][$i]}";
					}
					print "LIST: " . implode(' ', $titles) . "\n";
					print "LINK: http://www.imdb.com/title/{$match[0][1][$num]}/\n";
					$cacheFile 	= "IMDB.{$match[0][1][$num]}";
					$_Link[1]	= "/find?ss=tt&q={$match[0][1][$num]}";
					$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
					unset($match);
					if (!is_object($src)) {
						$match = array();
						preg_match('@<h1 class="header">\s*(.+)\s*<span>(.+)</span>@i', $src, $match[]);
						preg_match('@(?:<img width="\d+" alt="([^"]+)" src="http://i.media-imdb.com/images/\S+/certificates/us/\S+.png" class="absmiddle" title="[^"]+" height="\d+">&nbsp;&nbsp;)?(.+?)&nbsp;&nbsp;-&nbsp@i', $src, $match[]);
						preg_match_all('@<a\s+onclick="[^"]+"\s+href="/genre/([^"]+)"\s+>@i', $src, $match[]);
						preg_match('@<span class="rating-rating">([^<]+)<span>/10</span>@i', $src, $match[]);
						preg_match('@<h4 class="inline">\s+Director:\s+</h4>\s+<a\s+href="/name/nm\d+/">([^<]+)</a></div>@i', $src, $match[]);
						$genre = implode(", ", $match[2][1]);
						print "TITLE: {$match[0][1]}\n";
						print "YEAR: " . str_replace(array('(',')'),'',strip_tags($match[0][2])) . "\n";
						if (!empty($match[1][1])) print "RATING: {$match[1][1]}\n";
						print "LENGTH: {$match[1][2]}\nGENRE: {$genre}\nURATING: {$match[3][1]}/10\n";
						if (isset($match[4][1])) print "DIRECTOR: {$match[4][1]}\n";
					} else {
						catchErrorHandler;
					}
				}
			}
		} else {
			catchErrorHandler;
		}
	}
?>