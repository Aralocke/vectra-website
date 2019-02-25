<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) 
{
    exit;
}
$Levels = array (
    '0' => 'Unknown',
    '1' => 'Very Easy',
    '2' => 'Easy',
    '3' => 'Medium',
    '4' => 'Hard',
    '5' => 'Vary Hard',
    '6' => 'Master',
    '7' => 'Grandmaster'
) ;
$Search = null ;
if (!empty($_GET['search']))
{
    $Search = urldecode(trim($_GET['search'])) ;
}

# Credits
echo 'NOTICE: This data is kindly provided with permission by <a href="http://zybez.net">Zybez.net</a>' . chr(10) ;
if (empty($Search))
{
    echo 'ERROR: Missing argument &search'.chr(10) ;
}
else
{
    # Connect to the database
    $Dbc->connect() ;   
  
    if (is_numeric($Search))
    {
        $Query = "SELECT * FROM quest WHERE id = '".(int)$Search."'" ;
        $Result = $Dbc->sql_query($Query);
        $Result = ($Dbc->sql_num_rows($Result) > 0) ? $Result : false ;
    }
    else
    {
        $Tests = explode(' ', trim($Search));
        if ($Tests[0] == 'A' || $Tests[0] == 'The')
        { $Search = trim(substr($Search, strpos($Search, ' '))) ; }
        $Result = sqlSearch('Parsers', 'quest', 'name', $Search, 5) ;
    }
    
    if ($Result == false)
    {
        echo 'ERROR: Nothing found for your search ' . $Search . chr(10) ;
    }
    else
    {
        $Results = $Dbc->sql_num_rows($Result) ;
        echo 'RESULTS: '.$Results.chr(10) ;
        if ($Results > 1)
        {
            $Count = 0 ;
            while ((($Obj = $Dbc->sql_fetch($Result)) !== null) && $Count < 10)
            {
                echo 'QUEST: '.str_replace(' ', '_', $Obj->name) . ' #' . $Obj->id . chr(10);
                $Count++ ;
            }
        }
        else
        {
            $Obj = $Dbc->sql_fetch($Result);
            echo 'NAME: ' . str_replace(' ', '_', $Obj->name) . chr(10);
            echo 'MEMBERS: ' . (($Obj->members == 1)?'Yes':'No') . chr(10) ;
            echo 'QPS: ' . $Obj->qps . chr(10) ;
            echo 'REQS: ' . $Obj->reqs . chr(10) ;
            echo 'DIFFICULTY: ' . $Levels[(int)$Obj->difficulty] . chr(10) ;
            echo 'LENGTH: ' . $Levels[(int)$Obj->length] . chr(10) ;
            $Link = 'http://www.zybez.net/quest.aspx?id=' . $Obj->id ;
            echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10);          
        }
        $Dbc->sql_freeresult($Result) ;
    }
}