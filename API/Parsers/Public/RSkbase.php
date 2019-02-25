<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (empty($_GET["search"]))
{
    echo 'ERROR: You must specify something to search for &search' . chr(10) ;
}
else
{
    $Search = urlencode($_GET["search"]) ;
    $CacheFile = 'Kbase.' . md5($_GET["search"]) ;
    $Uri = '/kbase/search.ws?search_query=' . $Search . '&category=null&subcat=null&title_chk=1&keywords_chk=1&description_chk=1&body_chk=1&and_rad=1&submit=Search+Again';
    $Src = httpCacheSocket(
        'GET',
        'http://www.runescape.com',
        $Uri,
        $CacheFile,
        120
    ) ;

    if (!is_object($Src))
    {
        # Socket succeeded
        if (strpos($Src, "did not return any results"))
        {
            echo 'ERROR: There were no results that matched ' . $_GET["search"] . ' in the database.' . chr(10) ;
        }
        else
        {
            preg_match("/<a style=\"margin-left: 6px; text-decoration:none;\" href=\"(.*)\"><b>(.*)<\/b><\/a>/",
                $Src, $info) ;
            $link = $info[1] ;
            $title = $info[2] ;
            preg_match("/<b>(.*)<\/b>(.*)<b>(.*)/", $Src, $desc) ;
            $desc = strip_tags($desc[0]) ;
            preg_match("/(.*)<\/a><\/ul>/", $Src, $found) ;
            $found = str_replace("  ", " ", strip_tags(str_replace("&gt;", ">", $found[0]))) ;
            echo 'TITLE: ' . $title . chr(10) ;
            echo 'SECTION: ' . $found . chr(10) ;
            echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($link):$link) . chr(10) ;
            echo 'DESCRIPTION: ' . $desc . chr(10) ;
        }
    }
    else
    {
        # Socket failed
        cacheErrorHandler($Src) ;
    }
}

?>