<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}


$Table = 0 ;
$Skill = 0 ;
$Rank = 1 ;
if (!empty($_GET['table']) && (is_numeric($_GET['table']) && in_array($_GET['table'], array(0, 1))))
{
    $Table = (int)$_GET['table'] ;
}

if (!empty($_GET['skill']) && (is_numeric($_GET['skill']) && !empty($Skills[0][$_GET['skill']]	)))
{
    $Skill = (int)$_GET['skill'] ;
    if ($Skill > 25)
    {
        $Table = 1 ;
        $Skill -= 26 ;
    }
}

if (!empty($_GET['rank']) && (is_numeric($_GET['rank']) && $_GET['rank'] <= 2000000))
{
    $Rank = (int)$_GET['rank'] ;
}
$Uri = '/m=hiscore/overall.ws?table=' . $Skill . '&category_type=' . $Table . '&rank=' . $Rank ;
$CacheFile = 'RSRank.T' . $Table . 'S.' . $Skill . 'R.' . $Rank ;
$Src = httpCacheSocket(
    'GET',
    'http://services.runescape.com',
    $Uri,
    $CacheFile,
    120
) ;
if (!is_object($Src))
{
    # Socket succeeded
	preg_match('@<a href="compare\.ws\?user1=[^"]+" class="tableRow tableRowSelected">(.+?)</a>@is', $Src, $Match);
    //preg_match('#<td class="alL"><a style="color:(?:\#[\w\d]+);" href="hiscorepersonal\.ws\?user1=(?:.+?)">(.+?)</a></td>[\r\n]+<td class="alL ARow">(.+?)</td>(?:[\r\n]+<td class="alL ARow">(.+?)</td>)?#i',
    //    $Src, $Match) ;
    echo 'RANK: ' . $Rank . chr(10) ;
    echo 'TABLE: ' . (($Table == 0) ? 'Skills' : 'Minigames') . chr(10) ;
    echo 'SKILL: ' . $Skills[0][(($Table == 0) ? $Skill : $Skill+26)] . chr(10) ;
    if (empty($Match))
    {
        echo 'ERROR: No user is ranked at ' . $Rank . ' in the ' . $Skills[0][(($Table == 0) ? $Skill : $Skill+26)] .
            ' skill' . chr(10) ;
    }
    else
    {
		preg_match_all('@<span class="(?:rank|name|level|xp)Column">\s*<span>([^<]+)</span>\s*</span>@i', $Match[1], $Info);
        $Url = 'http://services.runescape.com'.$Uri ;
        echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url) : $Url) . chr(10) ;
        echo 'RSN: ' . str_replace('&nbsp;', '_', rawurldecode(htmlentities($Info[1][1]))) . chr(10) ;
        echo 'LEVEL: ' . $Info[1][2] . chr(10) ;
        if (!empty($Info[1][3]))
        {
            echo 'EXP: ' . $Info[1][3] . chr(10) ;
        }
    }
}
else
{
    # Socket failed
    cacheErrorHandler($Src) ;
}

?>