<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

#date_default_timezone_set('America/Los_Angeles');

if ($Dbc->connect() === false)
{
    echo 'Failed to connect to server ' . $Dbc->sql_get_connect_error() . chr(10) ;
}
else
{
    $Updating = isUpdating($Dbc) ;

    $Query = "
        SELECT *
        FROM `geupdate`
        ORDER BY `geupdate`.`update_time` DESC
    " ;
    $Result = $Dbc->sql_query($Query) ;

    if (!is_object($Result))
    {
        echo 'ERROR: Geupdate checker not running.' . chr(10) ;
        $Dbc->sql_freeresult($Result) ;
    }
    else
    {
        $Object = $Dbc->sql_fetch($Result) ;
        $Dbc->sql_freeresult($Result) ;

        if (is_object($Updating))
        {
            if (time() - $Object->update_time > 600)
            {
                $Duration = duration(time() - $Object->update_time) ;
            }
            else
            {
                $Duration = '10 Minute Wait.' ;
            }
            print 'STATUS: Updating' . chr(10) ;
            print 'STATUSTIME: ' . $Duration . chr(10) ;            
            $UpdateTime = $Object->update_time ;
            print 'LAST: ' . $UpdateTime . ':' . (time() - $UpdateTime) . ':' . duration(time() - $UpdateTime) . chr(10) ;
        }
        else
        {
            $UpdateTime = $Object->update_time ;
            print 'LAST: ' . $UpdateTime . ':' . (time() - $UpdateTime) . ':' . duration(time() - $UpdateTime) . chr(10) ;
        }

        $Result = $Dbc->sql_query('SELECT SUM(update_length), COUNT(update_length) FROM geupdate') ;
        $Object = $Dbc->sql_fetch($Result, MYSQL_NUM) ;
        $Dbc->sql_freeresult($Result) ;
        $Average = (int)ceil($Object[0] / $Object[1]) ;
        print 'AVERAGE: ' . duration($Average) . chr(10) ;

        $Result = $Dbc->sql_query('SELECT * FROM geupdate ORDER BY update_id DESC') ;
        $Object = $Dbc->sql_fetch($Result) ;
        $Dbc->sql_freeresult($Result) ;
        $Previous = $Object->update_length ;
        print 'PREVIOUS: ' . duration($Previous) . chr(10) ;
        
        #if (isset($_GET['full']))
        #{
            # GE Updates occur once a day between 3 AM and 9 PM GMT. [Never BST]  
            
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('Europe/London'));
            # var_dump($date) ;
            # echo  $date->format( 'H:i:s A  /  D, M jS, Y' ).chr(10);

            $Now = $date->getTimestamp() + (3600 * 7 + date('I')) ; 
            # echo 'NOW: ' . $Now . chr(10) ;                      
            #echo 'NOW->GMT: '.date('D H:i.s A', $Now).chr(10); 
            
            #echo 'NOW->LAX: '.date('D H:i.s A').chr(10);            
            
            $Frame['time']  = ($Now - (time() - $UpdateTime)) ;
            $Frame['open']  = mktime(3, 0, 0, date('m', $Now), date('d', $Now), date('Y', $Now)) ;
            $Frame['close'] = mktime(23, 59, 59, date('m', $Now), date('d', $Now), date('Y', $Now)) ;
            
            #echo 'time='.$Frame['time'].'='.date('D H:i.s A',$Frame['time']).chr(10) ;
            #echo 'open='.$Frame['open'].'='.date('D H:i.s A',$Frame['open']).chr(10) ;
            #echo 'boolean='.(String)($Frame['time'] >= $Frame['open']).chr(10) ;
            #echo 'close='.$Frame['close'].'='.date('D H:i.s A',$Frame['close']).chr(10) ;
            #echo 'boolean='.(String)($Frame['time'] <= $Frame['close']).chr(10) ;
            #echo 'diff='.($Frame['close'] - $Frame['open']).chr(10) ;
            
            $Today = (($Frame['time'] >= $Frame['open']) && ($Frame['time'] <= $Frame['close'])) ? true : false ;
            if ($Today)
            {
                $Open = mktime(3, 0, 0, date('m', $Now), date('d', $Now)+1, date('Y', $Now)) ;
                $Close = mktime(21, 0, 0, date('m', $Now), date('d', $Now)+1, date('Y', $Now)) ;
            }
            else
            {
                $Open = mktime(3, 0, 0, date('m', $Now), date('d', $Now), date('Y', $Now)) ;
                $Close = mktime(21, 0, 0, date('m', $Now), date('d', $Now), date('Y', $Now)) ;
            }
            # echo 'START: ' . $Open . chr(10) ;
            # echo 'OPEN->3pm: ' . date('D h:i.s A', $Open) . chr(10) ;
            
            echo 'UPDATEDTODAY: ' . (($Today) ? 1 : 0) . chr(10) ;
            echo 'NOTBEFORE: ' . (($Now >= $Open) ? 'Within timeframe' : duration($Open - $Now)) . chr(10) ;
            
            # echo 'END: ' . $Close . chr(10) ;
            # echo 'CLOSE->9pm: ' . date('D h:n.s A', $Close) . chr(10) ;
            
            echo 'WITHIN: ' . duration($Close - $Now) . chr(10) ; 
        #}        
    }
}
?>