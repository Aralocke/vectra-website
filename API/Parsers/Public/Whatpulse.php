<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (empty($_GET['user']))
{
    print 'ERROR: Missing argument &user' . chr(10) ;
}
else
{
    $Uri = (is_numeric($_GET['user'])) ? "/api/user.php?UserID=" . $_GET['user'] :
        "/stats/users/" . $_GET['user'] . "/normal/" ;
    $cacheFile = 'WhatPulse.' . $_GET['user'] ;

    $src = httpCacheSocket(
        "GET",
        'http://whatpulse.org',
        $Uri,
        $cacheFile,
        120
    ) ;

    if (!is_object($src))
    {
        if (stristr($src, 'Unknow UserID'))
        {
            echo 'ERROR: User ID ' . $_GET['user'] . ' not found' . chr(10) ;
        } elseif (stristr($src, 'Invalid UserID/AccountName!'))
        {
            echo 'ERROR: Username ' . $_GET['user'] . ' not found' . chr(10) ;
        }
        else
        {
            if (!is_numeric($_GET['user']))
            {
                $get = strpos($src, '<a href="/stats/users/') + strlen('<a href="/stats/users/') ;
                $end = strpos($src, '/" title="', $get) ;
                $uid = substr($src, $get, $end - $get) ;
                $cacheFile = 'WhatPulse.' . $uid ;
                $src = httpCacheSocket(
                    "GET",
                    'http://whatpulse.org',
                    '/api/user.php?UserID=' . $uid,
                    $cacheFile,
                    120
                ) ;
            }
            if (!is_object($src))
            {
                preg_match_all("#<(.+)>([^<]+)</.+>[\s\r\n]+#i", $src, $info) ;
                for ($i = 0; $i < count($info[1]); $i++)
                {
                    switch ($i)
                    {
                        case 5:
                            $Clean = htmlfree($info[0][5]) ;
                            $Time = time() - strtotime($Clean, time()) ;
                            print 'LASTPULSE: ' . duration($Time, 3) . chr(10) ;
                            break ;
                        case 24:
                            print "RANKINTEAM: " . htmlfree($info[0][24]) . chr(10) ;
                            break ;
                        default:
                            print strtoupper($info[1][$i]) . ': ' . html_entity_decode(stripslashes($info[2][$i])) . chr(10) ;
                            break ;
                    }
                }
            }
            else
            {
                cacheErrorHandler($src) ;
            }
        }
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>