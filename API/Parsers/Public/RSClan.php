<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$search = '' ;
if (!empty($_GET['search']))
    $search = urldecode($_GET['search']) ;

if (empty($search))
{
    $domain = 'http://services.runescape.com' ;
    $uri = '/m=clan-hiscores/landing.ws' ;
    $url = $domain . $uri ;
    $cache = 'ClanLanding' ;
    $source = httpCacheSocket('GET', $domain, $uri, $cache, 60) ;

    preg_match_all("#<li>[\r\n\s]+<a href=\"(.+?)\">[\r\n\s]+<span class=\"Olde\">(\d+)</span>[\r\n\s]+<span class=\"elide\">(.+?)</span>#i", $source, $matches) ;
    
    $titles = array('TOPCLANP2P', 'TOPCLANF2P', 'TOPKDRATIO', 'TOTALLEVEL', 'TOPXP', 'MEMBERS') ;
    
    for ($i = 0 ; $i < sizeof($titles); $i++)
    {
        echo $titles[$i].': ' ;
        for ($n = ($i * 5) ; $n < ($i * 5 + 5); $n++)
            echo str_replace(' ', '_', $matches[3][$n]) . ' ' ;
        echo "\n" ;
    }
}
else
{
    
}

?>