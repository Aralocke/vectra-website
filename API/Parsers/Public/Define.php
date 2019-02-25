<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$Word = '' ;
if (!empty($_GET['word']))
{
    $Word = urldecode(trim($_GET['word'])) ;
}
if (!empty($Word))
{
    $Filename = 'GoogleDefine.' . $Word ;

    $Src = httpCacheSocket(
        'GET',
        'http://google.com',
        '/search?sourceid=vectra-bot&ie=UTF-8&q=define:+' . urlencode($Word),
        $Filename,
        120
    ) ;

    if (!is_object($Src))
    {
        # Socket succeeded
        preg_match_all('@(<li>(?:[^<]+(?:\s<li>|<br>)){1,})@i', $Src, $Def) ;

        $Count = count($Def[1]) ;

        print 'TOTAL: ' . $Count . chr(10) ;

        for ($i = 0; $i < $Count; $i++)
        {
            echo 'DEFINE: ' . strip_tags(str_replace('<li>', '', preg_replace('/(?:\s{1,}<li>|(?:\s{1,})?<br>)/i',
                '; ', $Def[0][$i]))) . chr(10) ;
        }
    }
    else
    {
        # Socket failed
        cacheErrorHandler($Src) ;
    }
}
else
{
    echo 'ERROR: Missing arguement &word' . chr(10) ;
}

?>