<?php
	if (!defined('IN_PARSERS') || !IN_PARSERS) {
		exit;
	} else if (empty($_GET['tag'])) {
		print "ERROR: Missing argument `tag`\n";
	} else {
		$_Link 		= array("http://xboxapi.duncanmackenzie.net", "/gamertag.ashx?GamerTag=" . urlencode($_GET['tag']));
		$cOpts 		= array(
						CURLOPT_USERAGENT => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3 (.NET CLR 3.5.30729)",
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => 1
					  );
		$cacheFile 	= "XBL.GTag." . str_replace(' ', '_', $_GET['tag']);
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
		if (!is_object($src)) {
			$src 		= simplexml_load_file($src);
			$Info 		= array();
			$timeRegex 	= "#(\d+\-\d+\-\d+)T(\d+:\d+:\d+)([+-]\d+):\d+#";
			if ($src->PresenceInfo->Valid == "true") { 
				$Info['GamerTag']	= $src->Gamertag;
				$Info['Online'] 	= $src->PresenceInfo->Online;
				$Info['Rep'] 		= $src->Reputation;
				$Info['Country']	= $src->Country;
				$Info['AccStatus'] 	= $src->AccountStatus;
				preg_match($timeRegex, $src->PresenceInfo->LastSeen, $lastSeen);
				$Info['LastSeen'] 	= str_replace('-', '/', $lastSeen[1]) . " {$lastSeen[2]} GMT{$lastSeen[3]}";
				$Info['GamerScore'] = $src->GamerScore;
				$Info['Zone'] 		= $src->Zone;
				$Info['Games'] 		= array();
				for ($i = 0; $i < count($src->RecentGames->XboxUserGameInfo); $i++) {
					preg_match($timeRegex, $src->RecentGames->XboxUserGameInfo->$i->LastPlayed, $lastPlayed);
					$Info['Games'][$i] 	= array(
												"Title" 		=> $src->RecentGames->XboxUserGameInfo->$i->Game->Name,
												"Achievements" 	=> "{$src->RecentGames->XboxUserGameInfo->$i->Achievements}/{$src->RecentGames->XboxUserGameInfo->$i->Game->TotalAchievements}",
												"GamerScore" 	=> "{$src->RecentGames->XboxUserGameInfo->$i->GamerScore}/{$src->RecentGames->XboxUserGameInfo->$i->Game->TotalGamerScore}",
												"LastPlayed" 	=> str_replace('-', '/', $lastPlayed[1]) . " {$lastPlayed[2]} GMT{$lastPlayed[3]}"
											);
				}
				foreach ($Info as $header => $value) {
					if ($header != "Games") {
						print strtoupper($header) . ": " . $value . "\n";
					}
				}
				for ($i = 0; $i < count($Info['Games']); $i++) {
					$n = $i+1;
					print "GAME#{$n}: {$Info['Games'][$i]['Title']} [{$Info['Games'][$i]['Achievements']}|{$Info['Games'][$i]['GamerScore']}] ({$Info['Games'][$i]['LastPlayed']})\n";
				}
			} else {
				print "ERROR: Invalid or Unregistered tag.\n";
			}
		} else {
			cacheErrorHandler($src);
		}
	}
?>