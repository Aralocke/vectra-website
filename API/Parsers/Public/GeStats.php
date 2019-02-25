<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

# http://services.runescape.com/m=itemdb_rs/frontpage.ws?listview=0
# http://services.runescape.com/m=itemdb_rs/frontpage.ws?listview=1
$View = 0 ;
if (!empty($_GET['view']) && (is_numeric($_GET['view']) && $_GET['view'] > 0 && $_GET['view'] <= 1))
{
    $View = (int)trim($_GET['view']) ;
}
$CacheFile = 'GEStats' . $View ;
$Src = httpCacheSocket(
    'GET',
    'http://services.runescape.com',
    '/m=itemdb_rs/frontpage.ws?listview=' . $View,
    $CacheFile,
    600
) ;

if (!is_object($Src))
{
    if ($View == 0)
    {
        $Pattern = '#<td>(?:[\r\n\s]+)<a href=\"(.+?)\">(.+?)<\/a>(?:[\r\n\s]+)</td>(?:[\r\n\s]+)<td>(.+?)</td>(?:[\r\n\s]+)<td class=\"(?:[\w\d]+)\">(?:[\r\n\s]+)(?:<span class=\"(.+?)\">)?(?:[\r\n\s]+)(.+?)(?:[\r\n\s]+)<\/span>?#i' ;
    }
    else
    {
        $Pattern = '#<td>(?:[\r\n\s]+)<a href=\"(.+?)\">(.+?)<\/a>(?:[\r\n\s]+)</td>(?:[\r\n\s]+)<td>(.+?)</td>(?:[\r\n\s]+)<td class=\"(?:[\w\d]+)\">(?:[\r\n\s]+)(.+?)(?:[\r\n\s]+)<\/td>#i' ;
    }
    preg_match_all($Pattern, $Src, $Matches) ;
    # var_dump($Matches) ;
    $Links = $Matches[1] ;
    $Names = $Matches[2] ;
    $Price = $Matches[3] ;
    if (!empty($Matches[4]))
    {
        $Type = $Matches[4] ;
    }
    if (!empty($Matches[5]))
    {
        $Num = $Matches[5] ;
    }
    unset($Matches) ;
    # var_dump($Names) ;

    for ($i = 0; $i < count($Names); $i++)
    {
        if ($View == 0)
        {
            echo 'ITEM: ' . trim(substr($Links[$i], strpos($Links[$i], '?obj=') + 5)) . ' ' .
                str_replace(' ', '_', $Names[$i]) . ' ' . $Price[$i] . ' ' . (($Type[$i] ==
                'rise') ? '+' : '-') . $Num[$i] . '%' . chr(10) ;
        }
        else
        {
            echo 'ITEM: ' . trim(substr($Links[$i], strpos($Links[$i], '?obj=') + 5)) . ' ' .
                str_replace(' ', '_', $Names[$i]) . ' ' . $Price[$i] . ' ' . $Type[$i] . chr(10) ;
        }
    }
}
else
{
    # Socket failed
    cacheErrorHandler($Src) ;
}

?>