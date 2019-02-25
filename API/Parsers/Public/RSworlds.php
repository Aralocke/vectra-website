<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
$World = 0 ;
$Filter = '' ;
$Event = '' ;
$Members = '';

if (!empty($_GET['world']) && is_numeric($_GET['world']))
{
    $World = (int)(trim(urldecode($_GET['world']))) ;
}
if (!empty($_GET['filter']) && preg_match("#([<>=]|[><]=)(\d+(?:\.\d+)?k?)#i", $_GET['filter'], $Match))
{
    $Filter = $Match ;
    $Filter[2] = (int)stringToNum($Filter[2]) ;
}
if (!empty($_GET['event']))
{
    $Event = trim(urldecode($_GET['event'])) ;
}
if (isset($_GET['p2p']))
{
	if ($_GET['p2p'] == 1 OR $_GET['p2p'] == 0) $Members = $_GET['p2p'];
}
if (empty($World) && empty($Filter) && empty($Event))
{
    echo 'ERROR: Missing arguements &world' . chr(10) ;
}
else
{
    $Dbc->connect() ;
    
    if (!empty($World))
    {
        $Query = "
            SELECT *
            FROM `Parsers`.`rsworlds`
            WHERE `world` = '" . $Dbc->sql_escape($World) . "'
        " ;
        
        $Result = $Dbc->sql_query($Query) ;
        if ($Dbc->sql_num_rows($Result) == 0)
        {
            echo 'ERROR: No world ' . $World . ' found.' . chr(10) ;
        }
        else
        {
            $World = $Dbc->sql_fetch($Result) ;
            echo 'WORLD: ' . $World->world . chr(10) ;
            echo 'PLAYERS: ' . $World->players . chr(10) ;
            echo 'TYPE: ' . $World->location . chr(10) ;
            echo 'LOOTSHARE: ' . (($World->lootshare == 0) ? 'No' : 'Yes') . chr(10) ;
            echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($World->link) : $World->link) . chr(10) ;
            echo 'MEMBERS: ' . (($World->members == 0) ? 'No' : 'Yes') . chr(10) ;
            $Dbc->sql_freeresult($Result) ;
        }
    }
    elseif (empty($World) && (!empty($Filter) || !empty($Event)))
    {
		if (preg_match('/^((non?)?l(?:oot)?s(?:hare)?)/i', $Event, $Match))
        {
            $Query = "
			    SELECT `members`, `world`, `players` 
                FROM `Parsers`.`rsworlds` 
                WHERE `lootshare` = '".((empty($Match[2])) ? 1 : 0)."'
            " ;
            if (!empty($Filter))
            {
                $Query .= " AND `players` ".$Dbc->sql_escape($Filter[1])." " . $Dbc->sql_escape($Filter[2]) ;
            }
			if (!empty($Members))
			{
				$Query .= " AND `members` = '{$Members}'";
			}
        }
        else
        {
			$Error = '';
			$Query = "
				SELECT `members`, `world`, `players`
				FROM `Parsers`.`rsworlds`
			";
			if (!empty($Event)) 
			{
				$Query .= " WHERE `location` LIKE '%" . $Dbc->sql_escape($Event) . "%' ";
				$Error .= "{$Event}";
			} 
			if ($Members != NULL)
			{
				if (strpos($Query, "WHERE"))
				{
					$Query .= " AND `members` = '{$Members}'";
					$Error .= ($Members == 1) ? ", members only":", free worlds only";
				}
				else
				{
					$Query .= " WHERE `members` = '{$Members}'";
					$Error .= ($Members == 1) ? "Members only":"Free worlds only";
				}
			}
			if (!empty($Filter))
			{
				if (strpos($Query, "WHERE"))
				{
					$Query .= " AND `players` ".$Dbc->sql_escape($Filter[1])." " . $Dbc->sql_escape($Filter[2]);
					$Error .= " and {$Filter[1]}{$Filter[2]}";
				}
				else
				{
					$Query .= " WHERE `players` " . $Dbc->sql_escape($Filter[1]) . " " . $Dbc->sql_escape($Filter[2]);
					$Error .= "{$Filter[1]}{$Filter[2]}";
				}
			}
			$Query .= " ORDER BY `players` DESC";
		}
		$Result = $Dbc->sql_query($Query) ;
        if ($Dbc->sql_num_rows($Result) == 0)
        {
            echo "ERROR: No world(s) matching the given parameters ({$Error}) found." . chr(10) ;
        }
        else
        {
            while (($World = $Dbc->sql_fetch($Result)) != null)
            {
                echo sprintf("WORLD: %d %d %d", $World->members, $World->world, $World->players) . chr(10) ;
            }
            $Dbc->sql_freeresult($Result) ;
        }
    }
}
?>