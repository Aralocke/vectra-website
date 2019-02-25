<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
else
{
    $item = array() ;
    $cacheFile = "RS.news.RSSFEED" ;
    $src = httpCacheSocket(
        "GET",
        'http://services.runescape.com',
        '/m=news/latest_news.rss',
        $cacheFile,
        120
    ) ;
    if (!is_object($src))
    {
        preg_match_all("#<title>(.*)</title>#i", $src, $title) ;
        preg_match_all("#<category>(.*)</category>#i", $src, $cate) ;
        preg_match_all("#<link>(.*)</link>#i", $src, $link) ;
        preg_match_all("#<pubDate>\w+, (\d{1,2} \w+ \d{4}) (?:\d{2}[:]?){3} GMT</pubDate>#i", $src, $date) ;
        for ($i = 0; $i < 5; $i++)
        {
            print $i + 1 . ' ' . $title[1][$i + 1] . '|' . str_replace(' ', '-', $date[1][$i]) . '|' . $cate[1][$i] . '|' .
                ((SHORT_LINKS) ? Google::shortUrl($link[1][$i + 1]) : $link[1][$i + 1]) . chr(10) ;
        }
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>