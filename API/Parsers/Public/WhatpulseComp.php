<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (empty($_GET['u1']))
{
    print 'ERROR: Missing argument &u1' . chr(10) ;
} elseif (empty($_GET['u2']))
{
    print 'ERROR: Missing argument &u2' . chr(10) ;
}
else
{
    $oth = array("blah", "Keys", "Clicks") ;
    $cacheFile = array('WhatPulse.' . $_GET['u1'], 'WhatPulse.' . $_GET['u2']) ;

    $src1 = httpCacheSocket(
        "GET",
        'http://parsers.vectra-bot.net',
        '/index.php?type=Whatpulse&user=' . $_GET['u1'],
        $cacheFile[0],
        600
    ) ;

    if (!is_object($src1))
    {
        if (strstr($src1, 'not found'))
        {
            echo 'ERROR: User ' . $_GET['u1'] . ' not found' . chr(10) ;
        }
        else
        {
            $src2 = httpCacheSocket(
                "GET",
                'http://parsers.vectra-bot.net',
                '/index.php?type=Whatpulse&user=' . $_GET['u2'],
                $cacheFile[1],
                600
            ) ;
            if (!is_object($src2))
            {
                if (strstr($src2, 'not found'))
                {
                    echo 'ERROR: User ' . $_GET['u2'] . ' not found' . chr(10) ;
                }
                else
                {
                    preg_match_all("#(?:(Total(?:KeyCount|MouseClicks)): (\d+)|AccountName: (\w+))[\n]#i",
                        $src1, $u1) ;
                    preg_match_all("#(?:(Total(?:KeyCount|MouseClicks)): (\d+)|AccountName: (\w+))[\n]#i",
                        $src2, $u2) ;
                    for ($i = 1; $i < 3; $i++)
                    {
                        if ($u1[2][$i] > $u2[2][$i])
                        {
                            $comp = ">" ;
                        } elseif ($u1[2][$i] == $u2[2][$i])
                        {
                            $comp = "=" ;
                        }
                        else
                        {
                            $comp = "<" ;
                        }
                        print strtoupper($oth[$i]) . ": ".str_replace(' ', '_', $u1[3][0])." {$u1[2][$i]} {$comp} ".str_replace(' ', '_', $u2[3][0])." {$u2[2][$i]}\n" ;
                    }
                }
            }
            else
            {
                cacheErrorHandler($src2) ;
            }
        }
    }
    else
    {
        cacheErrorHandler($src1) ;
    }
}

?>