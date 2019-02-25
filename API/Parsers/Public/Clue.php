<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} if (empty($_GET['q'])) {
		print "ERROR: Missing argument `q`\n";
	} else {
		$_Link = array("http://www.tip.it", "/runescape/");
		if (preg_match("@^(\d\d\.\d\d [NS]) (\d\d\.\d\d [EW])$@i", $_GET['q'], $coords)) {
			$coord 		 = True;
			$_Link[1] 	.= "?page=treasure_trails_coords.htm";
		} else {
			$coord 		 = False;
			$_Link[1] 	.= "?treasure_trails";
		}
		$cacheFile = "TTrails." . str_replace(' ', '_', $_GET['q']);
		$cOpts = array(
						CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1
					);
		$src = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
		if (!is_object($src)) {
			$strings = array("<br />", "\r", "\n");
			$rep = array(' ', '', ' ');
			if ($coord == False) {
                $q = preg_replace("/([\]\\$^?.()|'\"*\[])/", "\\\\\${1}", $_GET['q']);
				preg_match('@(<tr>\s*<td align="left">(?:<a name="[^"]+"></a>|<i>)?' . $q . '.+?</tr>)@is', $src, $match);
				if (empty($match)) {
					print "ERROR: Nothing found for '{$_GET['q']}'. Please submit a more accurate query.\n";
				} else {
					preg_match_all('@<td(?: align="left")?>(.+?)</td>@is', $match[1], $return);
					for ($i = 0; $i < count($return[1]); $i++) {
						$text = preg_replace("@\s{2,}@", ' ', strip_tags(str_replace($strings, $rep, $return[1][$i])));
						$head = ($i > 0) ? "ANSWER" : "CLUE";
						print "{$head}: {$text}\n";
					}
				}
			} else {
				$str = array('N', 'S', 'E', 'W');
				$repl = array("North", "South", "East", "West");
				$lat = str_ireplace($str, $repl, $coords[1]); $lon = str_ireplace($str, $repl, $coords[2]);
				preg_match('@(<tr>\s*<td align="center">' . $lat . '</td>\s*<td align="center">' . $lon . '</td>\s*.+?</tr>)@is', $src, $match);
				if (empty($match)) {
					print "ERROR: Nothing found for coordinates '{$_GET['q']}'.\n";
				} else {
					preg_match_all('@<td(?: align="center")?>(.+?)</td>@is', $match[1], $return);
					print "Lat&Lon: {$coords[1]} {$coords[2]}\n";
					for ($i = 0; $i < count($return[1]); $i++) {
						if ($i > 1) {
							$text = preg_replace("@\s{2,}@", ' ', strip_tags(str_replace($strings, $rep, $return[1][$i])));
							if ($text == "Picture") {
								preg_match('@<a href="([^"]+)"@i', $return[1][$i], $link);
								$Link = "http://www.tip.it/runescape/{$link[1]}";
								print 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link):$Link) . chr(10) ;
							} else {
								print "LOCATION: {$text}\n";
							} 
						}
					}
					
				}
			}
		} else {
			cacheErrorHandler($src);
		}
	}
?>