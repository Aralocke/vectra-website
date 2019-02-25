<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$Lookup = null ;
$Results = 4 ;

if (!empty($_GET["item"]))
{
    $Lookup = str_replace(
        array('%20', '_'), ' ', 
        trim($_GET["item"])
    ) ;
}
if (!empty($_GET['results']))
{
    if (is_numeric($_GET['results']))
    {
        $Results = (int)$_GET['results'] ;
        if ($Results <= 0)
        {
            $Results = 4 ;
        }
    }
    elseif (trim($_GET['results']) == 'all')
    {
        $Results = '10' ;
    }
}
if (empty($Lookup))
{
    echo 'ERROR: Missing arguement &item' . chr(10) ;
}
#####
# Parse the $Lookup input
#####
else
{
    if (strstr($Lookup, ':'))
    {
        $Temp = explode(':', $Lookup) ;
        for ($i = 0; $i < count($Temp); $i++)
        {
            $Parse = trim($Temp[$i]) ;
            $Str = explode(' ', $Parse) ;
            $ParseNum = stringToNum($Str[0]) ;
            if (is_numeric($ParseNum) && $ParseNum > 0)
            {
                $NumOf = $ParseNum ;
            }
            $Item = (isset($NumOf) && count($Str) > 1) ? trim(substr($Parse, strlen($Str[0]) + 1)) : trim($Parse) ;
            $Temp[$i] = new stdClass ;
            $Temp[$i]->clean_search = (is_numeric($Item)) ? trim($Item) : preg_replace("#(?:[^A-Za-z ]+)#i", null, trim($Item)) ;
            $Temp[$i]->real_search = prepSearch(stemPhrase(trim($Item))) ;
            $Temp[$i]->amount = ((isset($NumOf) && count($Str) > 1) ? $NumOf : 1) ;
            unset($NumOf) ;
        }
        $Lookup = $Temp ;
    }
    else
    {
        $Parse = trim($Lookup) ;
        $Str = explode(' ', $Parse) ;
        $Num = stringToNum($Str[0]) ;
        if (is_int($Num) && $Num > 0)
        {
            $NumOf = $Num ;
        }
        $Item = (isset($NumOf) && count($Str) > 1) ? trim(substr($Lookup, strlen($Str[0]) + 1)) : trim($Lookup) ;
        $Lookup = array() ;
        $Obj = new stdClass ;
        $Obj->clean_search = trim($Item) ;
        $Obj->real_search = prepSearch(trim($Item)) ;
        $Obj->amount = ((isset($NumOf) && count($Str) > 1) ? $NumOf : 1) ;
        $Lookup[] = $Obj ;
    }
    
    if (!is_array($Lookup))
    {
        $Lookup = array() ;
        $Obj = new stdClass ;
        $Obj->clean_search = trim($Item) ;
        $Obj->real_search = prepSearch(trim($Item)) ;
        $Obj->amount = 1 ;
        $Lookup[] = $Obj ;
    }
    if (count($Lookup) > 6)
    {
        echo 'ERROR: Max search is 6 Items at a time.' . chr(10) ;
    }
    else
    {
        #####
        # Connect to the database here
        #####
        $Dbc->connect() ;
        
        $Stats = new stdClass ;
        $Stats->outdated = 0;
        $Stats->total = array (
            'Amount' => 0,
            'Actual' => 0,
            'TotalResults' => 0           
        ) ;
        $Stats->RiseAndFall = 0 ;
        $Stats->items = array() ;
        $Stats->IDs = array() ;
        $Stats->notFound = array() ;
        $Stats->isUpdating = isUpdating($Dbc) ;
        
        $Stype = 6 ;
        if (!empty($_GET['stype']) && (is_numeric($_GET['stype']) && $_GET['stype'] >= 1 && $_GET['stype'] <= 6))
        {
            $Stype = (int)$_GET['stype'] ;
        }
        
        #var_dump($Lookup) ;
        
        for ($i = 0; $i <count($Lookup); $i++)
        {
            $Search = $Lookup[$i] ;
            ####
            # Query the database
            ####
            $Result = searchQuery($Search, $Stype) ; ;
            $Results = $Search->results ;
            $Stats->total['TotalResults'] += $Results ;
            ####
            # Update the Stats Variable
            ####  
            if ($Results == 0)
            {
                $Stats->notFound[] = str_replace(' ', '_', $Search->clean_search) ;
            }     
            else
            {
                ####
                # Track teh number of output
                ####
                $OutCount = 0 ;
                while (($Obj = $Dbc->sql_fetch($Result)) !== null && $OutCount < 6)
                {
                     $OutCount++ ;
                     # var_dump($Obj);
                     ###################################
                     # Build the Item object
                     $Item = new Item ;
                     $Item->search_name = $Search->clean_search ;
                     $Item->amount      = (int)$Search->amount ;
                     $Item->real_name   = str_replace(' ', '_', ucwords($Obj->name)) ;
                     $Item->members     = ($Obj->members == 1) ? true : false ;
                     $Item->id          = (int)$Obj->id ;
                     $Item->rise        = ($Obj->rise[0] == '+') ? stringToNum(substr($Obj->rise, 1)) : stringToNum($Obj->rise) ;
                     $Item->price       = (int)$Obj->price ;
                     $Item->change      = round(($Item->rise / ($Item->price - $Item->rise)) * 100, 2) ;
                     # var_dump($Item) ;
                     ###################################
                     ###################################
                     # Check for Outdated                     
                     if ($Stats->isUpdating && $Obj->updated == 0)
                     {
                        $Stats->outdated++ ;
                     }
                     ###################################
                     
                     ###################################
                     # Calculate Amounts                     
                     $Stats->total['Actual'] += $Item->price ;
                     # Totals with amounts
                     $Stats->total['Amount'] += $Item->price * $Item->amount ;
                     # Total Rise & Fall for all
                     $Stats->RiseAndFall += $Item->rise ;
                     ####################################
                     
                     ####################################
                     # Fill in the extra data
                     $Stats->IDs[] = $Item->id ;
                     $Stats->items[] = $Item ;
                     ####################################
                     if ($Results == 0 || count($Lookup) > 1) 
                     {
                         # Just break no need to reprocess loop
                         $Dbc->sql_freeresult($Result) ;
                         break ;
                     }                     
                } //while                
                unset($Item) ;
                $Dbc->sql_freeresult($Result) ;
            } // else results were found     
        } //outer for loop
        if (!empty($Stats->notFound))
        {
            echo 'ERROR: These searches returned no results: ' . implode(' ', $Stats->notFound) . chr(10) ;
        }
        if (!empty($Stats->items))
        {
            ####################################
            # Process Results
            echo 'RESULTS: ' . count($Stats->items) . ' ' . $Stats->total['TotalResults'] . chr(10) ;
            echo 'TOTAL: ' . sprintf("%s|%s", $Stats->total['Actual'], $Stats->RiseAndFall) . chr(10) ;
            $GraphLink = 'http://tip.it/runescape/index.php?gec&itemid=' . $Stats->IDs[0] . '&graphitems=' . implode(',', $Stats->IDs) ;
            echo 'GRAPHS: ' . ((SHORT_LINKS) ? Google::shortUrl($GraphLink) : $GraphLink) . chr(10) ;
            if (count($Stats->items) == 1)
            {
                $RSlink = 'http://services.runescape.com/m=itemdb_rs/viewitem.ws?obj=' . $Stats->items[0]->id ;
                echo 'RSGRAPHS: ' . ((SHORT_LINKS) ? Google::shortUrl($RSlink) : $RSlink) . chr(10) ;
            }
            foreach ($Stats->items as $Num => $Item)
            {
                printf("ITEM: %d %s %d %d %s\n", $Item->members, $Item->real_name, $Item->rise, $Item->price, $Item->change) ;
                printf("EXTRA: %d %d %s \n", $Item->id, $Item->amount, ($Item->price*$Item->amount)) ;
                if (isset($_GET['track']) && $_GET['track'] === 'H3LLY3S' && sizeof($Stats->items) <= 2)
                {
                    $Query = "
                        SELECT `track_id`, `item_id`, `rise`, `update_num`, `item_price` 
                        FROM `Parsers`.`getracker` 
                        WHERE `getracker`.`item_id` = '".$Item->id."'
                        ORDER BY `getracker`.`update_num` DESC
                        LIMIT 28
                    " ;
                    #echo $Query . chr(10) ;
                    $Result = $Dbc->sql_query($Query) ;
                    $Results = $Dbc->sql_num_rows($Result) ;
                    if ($Results == 0 || $Results < 7)
                    {
                        continue ;
                    }
                    $Tracker = array() ;
                    $Track = 0 ;
                    $Count = 0 ;
                    $Last = 0 ;
                    echo 'TRACKER: ' ;
                    for ($Count = 0; ($Obj = $Dbc->sql_fetch($Result)) !== null; $Count++)
                    {                    
                     
                        $Track += ($Count == 0) ? ((int)$Item->price - (int)$Obj->item_price) : ($Last - (int)$Obj->item_price) ;
                        if (($Count + 1) % 7 == 0)
                        {
                            $Change = ((int)$Item->price - $Track) ;
                            $Change = ($Change > 0) ? round(($Track / $Change) * 100, 2) : 0 ;
                            echo $Track . ':' . $Change . ' ' ;
                            $Tracker[] = $Track ;
                        }
                        $Last = (int)$Obj->item_price ;
                    } 
                    echo chr(10) ;  
                    $Dbc->sql_freeresult($Result) ;
                }
            } //foreach            
            if ($Stats->total['Amount'] != $Stats->total['Actual'])
            {
                echo 'TOTALAMT: ' . $Stats->total['Amount'] . chr(10) ; 
            }            
            ####################################
        }
        #Encompassing total output bracket
    }
# Closing bracket for the else
}
#####
# Functions
#####
function searchQuery(&$Obj, $Type = 6)
{
    global $Dbc ;
    if (is_numeric($Obj->clean_search) && $Obj->clean_search > 0)
    {
        $Query = "
            SELECT * FROM `Parsers`.`ge_data` WHERE
            `ge_data`.`id` = '".$Dbc->sql_escape((int)$Obj->clean_search)."'
            LIMIT 1
        " ;
        $Obj->search_query = $Query ;
        $Result = $Dbc->sql_query($Query) ;
        $Obj->results = $Dbc->sql_num_rows($Result) ;
        return $Result ;
    }
    switch ($Type)
    {
        case 1:       
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break ;
        case 2:
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."' AND `ge_data`.`members` = '0'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            if ($Obj->results > 0)
            {
                return $Result ;
            }
            $Query = "
                SELECT itemid 
                FROM `Parsers`.`ge_acronyms`
                WHERE `acronym` = '".$Dbc->sql_escape($Obj->clean_search)."'
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Item = $Dbc->sql_fetch($Result) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            $Dbc->sql_freeresult($Result) ;
            if ($Obj->results > 0)
            {
                $Query = "
                    SELECT * FROM `Parsers`.`ge_data` WHERE
                    `ge_data`.`id` = '".$Dbc->sql_escape($Item->itemid)."' AND `ge_data`.`members` = '0'
                    LIMIT 1
                " ;
                $Obj->search_query = $Query ;
                $Result = $Dbc->sql_query($Query) ;
                $Obj->results = $Dbc->sql_num_rows($Result) ;
                return $Result ;
            }
            $Query = "
                SELECT *, MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "') AS score 
                FROM `Parsers`.`ge_data` 
                WHERE MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "' IN BOOLEAN MODE)
                AND `ge_data`.`members` = '0'
                ORDER BY score DESC
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break;
        case 3:
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."' AND `ge_data`.`members` = '1'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            if ($Obj->results > 0)
            {
                return $Result ;
            }
            $Query = "
                SELECT itemid 
                FROM `Parsers`.`ge_acronyms`
                WHERE `acronym` = '".$Dbc->sql_escape($Obj->clean_search)."'
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Item = $Dbc->sql_fetch($Result) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            $Dbc->sql_freeresult($Result) ;
            if ($Obj->results > 0)
            {
                $Query = "
                    SELECT * FROM `Parsers`.`ge_data` WHERE
                    `ge_data`.`id` = '".$Dbc->sql_escape($Item->itemid)."' AND `ge_data`.`members` = '1'
                    LIMIT 1
                " ;
                $Obj->search_query = $Query ;
                $Result = $Dbc->sql_query($Query) ;
                $Obj->results = $Dbc->sql_num_rows($Result) ;
                return $Result ;
            }
            $Query = "
                SELECT *, MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "') AS score 
                FROM `Parsers`.`ge_data` 
                WHERE MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "' IN BOOLEAN MODE)
                AND `ge_data`.`members` = '1'
                ORDER BY score DESC
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break;
        case 4:
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."'
                AND `ge_data`.`members` = '0'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break ;
        case 5:
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."'
                AND `ge_data`.`members` = '1'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break ;
        default:
            $Query = "
                SELECT * FROM `Parsers`.`ge_data` WHERE
                `ge_data`.`name` = '".$Dbc->sql_escape($Obj->clean_search)."'
                LIMIT 1
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            if ($Obj->results > 0)
            {
                return $Result ;
            }
            $Query = "
                SELECT itemid 
                FROM `Parsers`.`ge_acronyms`
                WHERE `acronym` = '".$Dbc->sql_escape($Obj->clean_search)."'
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Item = $Dbc->sql_fetch($Result) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            $Dbc->sql_freeresult($Result) ;
            if ($Obj->results > 0)
            {
                $Query = "
                    SELECT * FROM `Parsers`.`ge_data` WHERE
                    `ge_data`.`id` = '".$Dbc->sql_escape($Item->itemid)."'
                    LIMIT 1
                " ;
                $Obj->search_query = $Query ;
                $Result = $Dbc->sql_query($Query) ;
                $Obj->results = $Dbc->sql_num_rows($Result) ;
                return $Result ;
            }
            $Query = "
                SELECT *, MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "') AS score 
                FROM `Parsers`.`ge_data` 
                WHERE MATCH (`ge_data`.`name`) AGAINST ('" . $Dbc->sql_escape($Obj->real_search) . "' IN BOOLEAN MODE)
                ORDER BY score DESC
            " ;
            $Obj->search_query = $Query ;
            $Result = $Dbc->sql_query($Query) ;
            $Obj->results = $Dbc->sql_num_rows($Result) ;
            return $Result ;
        break;
    }
    return $Query ;
}
function prepSearch($String)
{
    $String = explode (' ', $String) ;
    for ($i = 0; $i < count($String); $i++)
    {
        if (preg_match("#^(?:[+-]{1})([\w\d]+)(?:[*])?$#i", trim($String[$i]), $Matches))
        {
            if (substr($String[$i], -1) != '*')
            {
                $String[$i] = $String[$i] . '*' ;
            }
        }
        else
        {
            if (substr($String[$i], -1) != '*')
            {
                $String[$i] = $String[$i] . '*' ;
            }
            if ($String[$i][0] != '-' && $String[$i][0] != '+')
            {
                $String[$i] = '+' . $String[$i] ;
            }
        }
    }
    return implode (' ', $String) ;
}
class Item
{
    public $real_name ;
    public $search_name ;
    public $members ;
    public $rise ;
    public $min ;
    public $market ;
    public $max ;
    public $id ;
    public $amount ; 
    public $change ;
    public $time ; 
    
    function __construct()
    {
        $this->search_name = null ;
        $this->real_name   = null ;
        $this->members     = false ;
        $this->id     = 0 ;
        $this->rise   = 0 ;
        $this->min    = 0 ;
        $this->market = 0 ;
        $this->max    = 0 ;
        $this->change = 0 ;
        $this->amount = 1 ;
        $this->time   = 0 ;
    } 
        
    function getTrackData($Time = 7) 
    {
        ####
        # Retrieve time in days of an item
        ####
        global $Dbc ;
    }
}
?>