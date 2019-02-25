<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$Switch = '' ;
if (!empty($_GET['switch']))
{
    $Switch = strtoupper(trim($_GET['switch'])) ;
}
if (empty($Switch))
{
    echo 'ERROR: supply a &switch' . chr(10) ;
}
else
{
    $Src = '' ;
    if ($Switch == 'FML')
    {
        $Src = httpCacheSocket(
            'GET',
            'http://www.fmylife.com',
            '/random'
        ) ;
    } elseif ($Switch == 'LML')
    {
        $Src = httpCacheSocket(
            'GET',
            'http://www.lmylife.com',
            '/?sort=random'
        ) ;
    }

    if (!empty($Src))
    {
        if (!is_object($Src))
        {
            # Socket succeeded
            if ($Switch == "FML")
            {
                preg_match('@<div class="post" id="(\d+)"><p><a href="/([^/]+)/\1" class="fmllink">(.+?)</a></p><div class="date">@i',
                    $Src, $Match) ;
                    print strtoupper($Switch) . ': ' . trim(html_entity_decode(strip_tags($Match[3]))) . chr(10) ;
            }
            else
            {
                preg_match('@<p>(.+)</p>@i', $Src, $Match) ;
                print strtoupper($Switch) . ': ' . trim(html_entity_decode(strip_tags($Match[1]))) . chr(10) ;
            }
            # var_dump($Match) ;            
        }
        else
        {
            # Socket failed
            cacheErrorHandler($Src) ;
        }
    }
}

?>