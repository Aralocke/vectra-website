<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (empty($_GET['query']))
{
    print 'ERROR: Missing arguement &query' . chr(10) ;
}
else
{
    $qfc = (preg_match("/^\d+[\-\,]\d+[\-\,]\d+[\-\,]\d+$/", $_GET['query'])) ? true : false ;
    if ($qfc)
    {
        $Uri = "/forums.ws?" . str_replace('-', ',', $_GET['query']) ;
    }
    else
    {
        $Uri = "/searchthreads.ws?srcstr=" . urlencode($_GET['query']) .
            "&serch&search=Search" ;
    }

    $cacheFile = "RSForum." . str_replace(array(' ', '-'), '_', $_GET['query']) ;
    $src = httpCacheSocket(
        "GET",
        'http://forum.runescape.com',
        $Uri,
        $cacheFile,
        120
    ) ;

    if (!is_object($src))
    {
        if ($qfc)
        {
            $post = array() ;
            preg_match('#<a href="forums\.ws\?\d+,\d+,thd,\d+,\d+"><span>(.+?)</span></a>#i',
                $src, $post[0]) ;
            preg_match('#<div class="title thrd">(.+?)</div>#i', $src, $post[1]) ;
            preg_match('#<li>Page <input type="text" class="textinput" name="start" size="2" value="\d"/> of (\d+)</li>#i',
                $src, $post[2]) ;
            /*preg_match('#<div class="msgcreator uname" >[\r\n]+(?:<img .*?>&nbsp;)?[\r\n]+(.+)[\r\n]+</div>#i',
                $src, $post[3]) ;
				Original Author Match - No longer working
				*/
				preg_match('#<span class="author CssMO">(\S+)</span>#i', $src, $post[3]) ;
            preg_match('#<div class="msgtime">[\r\n]+(\d+\-\w+\-\d+ \d+:\d+:\d+)#i', $src, $post[4]) ;
			if (strpos($src,"Thread was not found")) 
			{

			echo "ERROR: The query $_GET[query] returned an invalid thread.\n";
			}
			else {
				$Url = 'http://forum.runescape.com' . $Uri;
            echo 'SECTION: ' . $post[0][1] . chr(10) ;
            echo 'TITLE: ' . $post[1][1] . chr(10) ;
            echo 'AUTHOR: ' . $post[3][1] . chr(10) ;
			echo 'POSTS: ' . $post[2][1] . chr(10) ;
            echo 'LASTPOST: ' . duration(time() - strtotime($post[4][1], time()), 3) . chr(10) ;
            echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url) . chr(10) ;
        }
		}
        else
        {
            $post = array() ;
            preg_match_all('#<a href="forums\.ws\?\d+,\d+,\d+,\d+">(.+)</a>#i', $src, $post[0]) ;
            preg_match_all('#<a href="(forums\.ws\?\d+,\d+,\d+,\d+)">#i', $src, $post[1]) ;
            preg_match_all('#<a href="forums\.ws\?\d+,\d+">(.+)</a>#i', $src, $post[2]) ;
            preg_match_all('#<td class="last_updated">[\r\n]+(\d+\-\w+\-\d+ \d+:\d+:\d+)[\r\n]+</td>#i',
                $src, $post[3]) ;
            $Count = (count($post[0][1]) > 5) ? 5 : count($post[0][1]) ;
            echo 'RESULTS: ' . $Count . chr(10) ;
            for ($i = 0; $i < $Count; $i++)
            {
                $Time = str_replace('-', ' ', $post[3][1][$i]) ;
                $Duration = abs(time() - strtotime($Time, time())) ;
				$Url = 'http://forum.runescape.com/' . $post[1][1][$i];
				print 'RSFORUM';
               /* print $i + 1 . ': ' . $post[0][1][$i] . '|' . 'http://forum.runescape.com/' . $post[1][1][$i] .
                    '|' . $post[2][1][$i] . '|' . $Time . '|' . $Duration . '|' . duration($Duration, 3) ;
                    */	
				 print $i + 1 . ': ' . $post[0][1][$i] . '|' . $post[2][1][$i] . '|' . $Time . '|'
				 . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url);
				  echo chr(10) ;
            }
        }
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>