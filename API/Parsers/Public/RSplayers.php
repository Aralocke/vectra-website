<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
else
{
    $cacheFile = "RS.Players" ;
    $src = httpCacheSocket(
        "GET",
        'http://www.runescape.com',
        '/title.ws',
        $cacheFile,
        120
    ) ;
    if (!is_object($src))
    {
        preg_match('#<span id="playerCount">(.+) people currently online</span>#i',
            $src, $players) ;
        $playr = str_replace(',', '', $players[1]) ;
        
        $Dbc->connect() ;
        
        $Query = 'SELECT `players` FROM `Parsers`.`rsworlds`' ;
        $Result = $Dbc->sql_query($Query) ;
        $Worlds = $Dbc->sql_num_rows($Result) ;
        $Dbc->sql_freeresult($Result) ;
        
        echo 'PLAYERS: ' . $players[1] . chr(10) ;
        echo 'AVERAGE: ' . round($playr / 165, 3) . chr(10) ;
        echo 'SERVERS: ' . $Worlds . chr(10) ;
        echo 'CAPACITY: ' . round($playr / (165 * 2000) * 100, 2) . '%' . chr(10) ;
    }
    else
    {
        cacheErrorHandler($src) ;
    }
}

?>