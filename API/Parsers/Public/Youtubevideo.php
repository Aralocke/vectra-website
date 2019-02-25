<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (empty($_GET['id']))
{
    print 'ERROR: Missing argument &id' . chr(10) ;
}
else
{   
    $cacheFile = 'YouTube.Video.' . $_GET['id'] ;
    $src = httpCacheSocket(
        "GET",
        'http://gdata.youtube.com',
        '/feeds/api/videos/' . $_GET['id'],
        $cacheFile, 
        120
    ) ;

    if (!is_object($src))
    {
        
        if (stristr($src, 'Invalid id'))
        {
            echo 'ERROR: Video not found or malformed id' . chr(10) ;
        }
        else
        {
            # <author><name>HIIMDAVID12345555</name><uri>http://gdata.youtube.com/feeds/api/users/hiimdavid12345555</uri></author>
            preg_match('@<author><name>(.+?)</name><uri>(.+?)</uri></author>@i', $src, $Author) ;
            # <gd:comments><gd:feedLink href='http://gdata.youtube.com/feeds/api/videos/nLa3H8llurw/comments' countHint='14'/></gd:comments>
            preg_match("@<gd:comments><gd:feedLink href='(.+?)' countHint='(\d+)'/></gd:comments>@i",
                $src, $Comments) ;
            # <media:group><media:category label='Entertainment' scheme='http://gdata.youtube.com/schemas/2007/categories.cat'>Entertainment</media:category>
            preg_match("@<media:category label='(.+?)' scheme='(.+?)'>(.+?)</media:category>@i",
                $src, $Category) ;
            # <media:keywords>(.+?)</media:keywords>
            preg_match("@<media:keywords>(.+?)</media:keywords>@i", $src, $Keywords) ;
            # <media:title type='plain'>ZGMF-X20A Strike Freedom Gundam</media:title>
            preg_match("@<media:title type='.+'>(.+?)</media:title>@i", $src, $Title) ;
            # <yt:duration seconds='153'/>
            preg_match("@<yt:duration seconds='(\d+)'/>@i", $src, $Duration) ;
            # <gd:rating average='1.5714285' max='5' min='1' numRaters='21' rel='http://schemas.google.com/g/2005#overall'/>
            preg_match("@<gd:rating average='(.+?)' max='(\d+)' min='(\d+)' numRaters='(\d+)' rel='(.+?)'/>@i",
                $src, $Stats) ;
            # <yt:statistics favoriteCount='4' viewCount='14458'/>
            preg_match("@<yt:statistics favoriteCount='(\d+)' viewCount='(\d+)'/>@i", $src,
                $Views) ;
            echo 'TITLE: ' . $Title[1] . chr(10) ;
            echo 'AUTHOR: ' . $Author[1] . chr(10) ;
            echo 'CATEGORY: ' . $Category[3] . chr(10) ;
            echo 'DURATION: ' . $Duration[1] . chr(10) ;
            echo 'VIEWS: ' . $Views[2] . chr(10) ;
            echo 'RATING: ' . $Stats[1] . ' ' . $Stats[4] . chr(10) ;
            echo 'COMMENTS: ' . $Comments[2] . chr(10) ;
            echo 'KEYWORDS: ' . $Keywords[1] . chr(10) ;
        }
        
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}
?>