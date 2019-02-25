<?php
	if (!defined('IN_PARSERS') OR !IN_PARSERS) {
		exit;
	} else {
		if (empty($_GET['user']) OR empty($_GET['game'])) {
			print "ERROR: Missing argument `user` or `game`\n";
		} else {
			if (strlen($_GET['user']) > 15) {
				print "ERROR: Gamertag too long. Must be 15 characters or less.\n";
			} else {
				$game 		= array(
									"halo3" => "halo3/careerstats.aspx", 
									"odst" 	=> "halo3/careerstatsodst.aspx",
									"reach" => "Reach/default.aspx"
								);
				$_game 		= strtolower($_GET['game']);
				if (empty($game[$_game])) {
					print "ERROR: Invalid game supplied\n";
				} else {
					$get 		= str_replace(' ', "%20", $_GET['user']);
					$_Link 		= array("http://www.bungie.net", "/stats/" . $game[$_game] . "?player={$get}");
					$cOpts 		= array(
										CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
										CURLOPT_FOLLOWLOCATION => TRUE,
										CURLOPT_RETURNTRANSFER => 1,
										CURLOPT_FRESH_CONNECT => 1
								);
					$cacheFile 	= "Halo.{$game[$_game]}.{$get}";
					$bad 		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
					$cacheFile 	= str_replace($bad, '_', $cacheFile);
					$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
					if (!is_object($src)) {
						if (stristr($src, "<div>No Players Found</div>") == TRUE OR stristr($src, "Service Record Not Found")) {
							print "ERROR: No service record found for ".str_replace(' ', '_', $_GET['user'])." in `" . strtoupper($_GET['game']) . "`\n";
						} else {
							switch ($_game) {
								case "reach":
									preg_match('@<ul class="alternatingList">(.+?)</ul>@is', $src, $info);
									preg_match('@<img id="\S+GlobalRankImage" title="([^"]+)" class="img_rankIcon" src="[^"]+" alt="\\1" style="[^"]+" /></a>\s*<h2><a href="[^"]+" id="\S+gamerTag">([^<]+)</a> - (\S+) ?</h2>@i', $src, $userInfo);
									preg_match_all('@<li(?: class="alt")?><strong>([^>]+)</strong><span id="[^"]+">([^<]+)</span></li>@i', $info[1], $Stats);
									print "GTAG: {$userInfo[2]}\nSERVICETAG: {$userInfo[3]}\nGLOBALRANK: {$userInfo[1]}\n";
									for ($i=0;$i<count($Stats[1]);$i++) print strtoupper(str_replace(' ', '', $Stats[1][$i])) . ": {$Stats[2][$i]}\n";
									break;
								
								case "halo3":
									$_userInfo = array("GTAG", "SERVICETAG", "GLOBALRANK");
									preg_match('@<div class="div2 halo3">(.+?)</div>\s*<div class="clear"></div>@is', $src, $info);
									preg_match('@<div class="top">(.+?)</div>\s*<div id="ctl00_mainContent_rptWeapons_ctl00_ctl00_pnlWeapon"@is', $src, $toolOfDes);
									preg_match('@<div class="title">\s*(.+)\s*</div>@i', $toolOfDes[1], $toolName);
									preg_match_all('@<div class="description">([^<]+)</div>\s*<div class="number">([^<]+)</div>@i', $toolOfDes[1], $toolStats);
									preg_match('@<ul>\s*<li><h3>(.+) - (\S+) ?</h3></li>\s*<li class="firstline" style="[^"]+"><span style="[^"]+"><span id="[^"]+">Global Rank: ([^<]+)</span></span>@i', $src, $userInfo);
									preg_match_all('@<p class="gtBlockHead">\s*<span>([^:]+):</span>\s*(\d+)\s*</p>@i', $info[1], $gamerCard);
									preg_match_all('@<t[hd] class="statTableLeft"><p class="textWrap">([^:]+):</p></t[hd]>\s*<t[hd] class="statTableRight"><p class="textWrap">([^<]+)</p></t[hd]>@i', $info[1], $Stats);
									for ($i=1;$i<count($userInfo);$i++) print $_userInfo[$i-1] . ": " . $userInfo[$i] . "\n";
									for ($i=0;$i<count($gamerCard[1]);$i++) print str_replace(' ', '', strtoupper($gamerCard[1][$i])) . ": " . $gamerCard[2][$i] . "\n";
									for ($i=0;$i<count($Stats[2]);$i++) {
										$out = str_replace(' ', '', strtoupper($Stats[1][$i])) . ": " . $Stats[2][$i] . "\n";
										$header = ($i < 4) ? "RANKED":"SOCIAL";
										print ($i == 0 OR $i == 4) ? $out:"{$header}{$out}";									
									}
									print "TOOLOFDESTRUCTION: {$toolName[1]}\n";
									for ($i=0;$i<count($toolStats[1]);$i++) print "TOD" . strtoupper($toolStats[1][$i]) . ": {$toolStats[2][$i]}\n";
									break;
									
								case "odst":
									$_Stats = array(
													0 	=> array("POINTS", "P/G", "P/D", "P/K"), 
													1 	=> array("KILLS", "K/G", "K/D"), 
													2 	=> array("DEATHS", "D/G"), 
													3 	=> array("GAMES")
												);
									preg_match('@<div class="overallcard">(.+?)</div>\s*<div class="clear"></div>@is', $src, $info);
									preg_match('@<div class="weapons">\s*<div class="entry">(.+?)</div>\s*<div class="entry">@is', $src, $toolOfDes);
									preg_match('@<div class="title">([^<]+)</div>@i', $toolOfDes[1], $toolName);
									preg_match_all('@<li class="([^"]+)">([^<]+)</li>@i', $toolOfDes[1], $toolStats);
									preg_match_all('@<ul>\s*<li><h3>(.+) - (\S+) ?</h3></li>\s*<li class="firstline">Firefight Games:@i', $src, $userInfo);
									preg_match_all('@<a id="ctl00_mainContent_psPlayer_hypHighScore" href="[^"]+">(\S+)</a> </li>@i', $info[1], $HighScore);
									preg_match_all('@<li class="first">(\S+)</li>@i', $info[1], $Stats);
									for ($i=0;$i<count($Stats[1]);$i++) $Stats[1][$i] = str_replace(',', '', $Stats[1][$i]);
									print "GTAG: {$userInfo[1][0]}\nSERVICETAG: {$userInfo[2][0]}\n";
									print "HIGHSCORE: {$HighScore[1][0]}\n";
									for ($i=0;$i<count($_Stats);$i++) {
										for ($m=0;$m<count($_Stats[$i]);$m++) {
											if ($m == 0) print "{$_Stats[$i][$m]}: {$Stats[1][$i]}\n";
											else print "{$_Stats[$i][$m]}: " . round($Stats[1][$i]/$Stats[1][4-$m], 1) . "\n";
										}
									}
									print "TOOLOFDESTRUCTION: {$toolName[1]}\n";
									for ($i=0;$i<count($toolStats[1]);$i++) print strtoupper(str_replace(' ', '', $toolStats[1][$i])) . "WITHTOD: {$toolStats[2][$i]}\n";
									break;
									
							}
                            $Link = implode('', $_Link) ;
							print "LINK: " . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . "\n";
						}
					} else {
						cacheErrorHandler($src);
					}
				}
			}
		}
	}
?>
