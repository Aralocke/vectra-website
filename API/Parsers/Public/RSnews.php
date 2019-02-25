<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
else
{
    $cacheFile = "RS.news" ;
    $src = httpCacheSocket(
        "GET",
        'http://services.runescape.com',
        '/m=news/latest_news.rss',
        $cacheFile,
        600
    ) ;
    if (!is_object($src))
    {
        @preg_match_all('@<title>(.[^\r\n]+)</title>@i', $src, $Title) ;
        @preg_match_all('@<category>(.[^\r\n]+)</category>@i', $src, $Cat) ;
        @preg_match_all('@<link>(.[^\r\n]+)</link>@i', $src, $Link) ;
        @preg_match_all('@<pubDate>(.[^\r\n]+)</pubDate>@i', $src, $Date) ;
        
        $Title = $Title[1] ;
        $Cat   = $Cat[1] ;
        $Link  = $Link[1] ;
        $Date  = $Date[1] ;
        
        print 'ARTICLES: ' . (count($Title) - 1) . "\n" ;
        for ($i = 1; $i < count($Title); $i++)
        {
            if (empty($Title[$i]) || empty($Cat[$i]) || empty($Date[$i]))
            {
                continue ;
            }
            $T = utf8_encode(rawurldecode(trim($Title[$i]))) ;
            $L = (SHORT_LINKS) ? Google::shortUrl($Link[$i]) : $Link[$i] ;
            echo 'NEWS: ' . $T . '|' . trim($Cat[$i]) . '|' . strtotime($Date[$i], time()) . '|' . $L . chr(10) ;
        }
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>