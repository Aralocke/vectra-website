<?php
    if (!defined('IN_PARSERS') OR !IN_PARSERS)
        exit;
    elseif (empty($_GET['q']) OR empty($_GET['search']))
        print "ERROR: Missing argument &clan OR &q\n";
    else {
        $q                  = str_replace(' ', '%20', $_GET['q']);
        $_Link              = array("http://services.runescape.com", (($_GET['search'] == 'clan') ? "/m=clan-home/clan/{$q}":""));
        $cOpts              = array(CURLOPT_FRESH_CONNECT => 1);
        $bad                = array('\\', '/', ':', '*', '?', '"', '<', '>', '|',' ');
        $cacheFile          = "RSClan." . str_replace($bad, '_', $_GET['q']);
        $source             = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120, $cOpts, 0);
        if (!is_object($source)) {
            if (stristr($source, "The clan was not found"))
                print "ERROR: No results found for {$_GET['q']}\n";
            else {
                $clanInfo   = array();
                preg_match('@<h4 class="PageHeading">([^<]+)</h4>\s*<h5>([^<]+)</h5>@i', $source, $clanInfo[]);
                preg_match_all('@<span class="Clanstat" id="Clanstat_[1-4]">([^<]+)<span class="Tooltip">@i', $source, $clanInfo[]);
                preg_match('@<div class="text">\s*<p>(.+?)</p>@i', $source, $clanInfo[]);
                preg_match('@<b class="Olde">Recruiting:</b>\s*([^<]+)</p>@i', $source, $clanInfo[]);
                $_Link[1]   = "/m=clan-hiscores/compare.ws?clanName={$q}";
                $source     = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile."-2", 120, $cOpts, 0);
                if (!is_object($source)) {
                    preg_match('@<span>\s*Combat Level \(average\)</span>\s*</span>\s*<div class="statValueLeftColumn">\s*<span>\s*([^\s<]+)\s*</span>@i', $source, $clanInfo[]);
                    preg_match_all('@<h4 class="PageHeading ">(?:Constitution|Ranged|Magic)</h4>\s*<div class="tableRow">\s*<span class="statNameColumn">\s*<span>Skill Level \(all\)</span>\s*</span>\s*<a class="statValueLeftColumn" href="[^"]+">\s*<span>\s*.+\s*</span>\s*</a>\s*<div class="statValueRightColumn"></div>\s*</div>\s*<div class="tableRow">\s*<span class="statNameColumn">\s*<span>Skill Level \(average\)</span>\s*</span>\s*<div class="statValueLeftColumn">\s*<span>\s*([^\s<]+)\s*</span>@i', $source, $clanInfo[]);
                    if (isset($_GET['skill']) AND in_array($_GET['skill'], $Skills[1]) AND strtolower($_GET['skill']) != "overall") {
                        preg_match('@<h4 class="PageHeading ">'.$_GET['skill'].'</h4>(.+)<h4 class="PageHeading@isU', $source, $statInfo);
                        preg_match_all("@<span>\s*([^\s<]+)\s*</span>\s*</(?:a|div)>@i", $statInfo[1], $clanInfo[]);
                    }
                } else
                    cacheErrorHandler($source);
                $header     = array("CLAN",          "QUOTE",         "MEMBERS",          "AVGCOMBAT",     "AVGTOTAL",         "KDRATIO",          "XP",               "CONSTITUTION",     "RANGED",           "MAGIC");
                $values     = array($clanInfo[0][1], $clanInfo[0][2], $clanInfo[1][1][0], $clanInfo[4][1], $clanInfo[1][1][1], $clanInfo[1][1][2], $clanInfo[1][1][3], $clanInfo[5][1][0], $clanInfo[5][1][1], $clanInfo[5][1][2]);
                print "CLAN: {$clanInfo[0][1]}\nQUOTE: {$clanInfo[0][2]}\n";
                for ($i = 2; $i < 10; $i++)
                    print "{$header[$i]}: " . $values[$i] . "\n";
                print "ABOUT: " . str_replace(array("<br><br>", "<br>"), ' ', $clanInfo[2][1]) . "\n";
                print "RECRUITING: {$clanInfo[3][1]}\n";
                if (isset($clanInfo[6])) {
                    $statH      = array("SKILLAVG", "MAXED");
                    $n          = 0;
                    for ($i = 1; $i < 4;) {
                        print "{$statH[$n]}: {$clanInfo[6][1][$i]}\n";
                        $i += 2; $n++;
                    }
                }
            } 
        } else
            cacheErrorHandler($source);
    }
?>
