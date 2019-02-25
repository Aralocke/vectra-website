<?php

if (!defined('IN_PARSERS') or !IN_PARSERS)
{
    exit ;
}

if (empty($_GET['q']))
{
    print "ERROR: Missing argument `q`\n" ;
}
else
{
    $ShortURL = (preg_match("@^https?://@i", $_GET['q'])) ? $_GET['q'] : "http://{$_GET['q']}" ;
    $ShortURL = urlencode($ShortURL) ;
    $_Link = array(
        "http://api.longurl.org",
        "/v2/expand?url=" . $ShortURL ."&format=json"
    ) ;
    
    $cOpts = array(
        CURLOPT_USERAGENT => "Vectra Crawler Bot (http://vectra-bot.net)"
    ) ;
    
    $bad = array('\\', '/', ':', '*', '?', '"', '<', '>', '|') ;
    $cacheFile = "LongURL." . str_replace($bad, '_', $_GET['q']) ;
    
    $src = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 86400, $cOpts, 0) ;
    if (!is_object($src))
    {
        $src = json_decode($src, true) ;
        $LongURL = $src['long-url'] ;
        print ($LongURL == $_GET['q']) ? "ERROR: Invalid ShortURL\n" : "LONGURL: {$LongURL}\n" ;
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>