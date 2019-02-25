<?php

###
# printParserList(void)
# prints the links to any parser in the public directory
###
function printParserList($Directory = PUBLIC_PARSER_DIR)
{
    if (false !== ($Handle = opendir($Directory)))
    {
        $Parsers = array() ;
        while (false !== ($File = readdir($Handle)))
        {
            if (is_file($Directory . $File))
            {
                $Name = substr($File, 0, strpos($File, '.')) ;
                $Parsers[$Name] = 'PARSER: <a href="?type=' . $Name . (isset($_GET['debug']) ?
                    '&debug=true' : '') . '">' . $Name . '</a>' . chr(10) ;
            }
        }
        closedir($Handle) ;
        if (empty($Parsers))
        {
            echo 'ERROR: No active parsers found for public use.' . chr(10) ;
        }
        else
        {
            ksort($Parsers) ;
            foreach ($Parsers as $Parser)
            {
                echo $Parser ;
            }
        }
    }
    else
    {
        echo "PHP: Unable to open Public parser directory\n" ;
    }
}

###
# $CacheFile: file name to cache to
# $File: data that we are caching
###
function cacheFile($CacheFile, &$File)
{

    if (empty($CacheFile) || empty($File))
    {
        return false ;
    }

    $Filename = CACHE_DIR . substr(sha1($CacheFile), 0, 15) . '._cache' ;
    file_put_contents($Filename, serialize($File)) ;    
    return chmod($Filename, 0777) ;
}

###
# $File: Cached file, file name
# $Expiry: max length the cache is valid
###
function checkCache($CacheFile, &$Expiry)
{
    $Filename = CACHE_DIR . substr(sha1($CacheFile), 0, 15) . '._cache' ;
    if (@file_exists($Filename))
    {
        if ((time() - $Expiry) > filemtime($Filename))
        {
            unlink($Filename) ;
            return false ;
        }
        $Cache = file_get_contents($Filename) ;
        return unserialize($Cache) ;
    }
    return false ;
}

###
# $File: Cached file, file name
# $Expiry: max length the cache is valid
###
function deleteCache($CacheFile)
{
    $Filename = CACHE_DIR . substr(sha1($CacheFile), 0, 15) . '._cache' ;
    if (@file_exists($Filename))
    {
        if (unlink($Filename))
        {
            return true ;
        }
        return false ;
    }
    return false ;
}

###
#    $Socket: GET/POST
#    $Domain: http://somedomain.com
#    $Uri: /somepage.php?search=blah
#    $CacheFile: Filename where the cache would be found
#    $Expriy: how long to cache for, default is 1 minute
#    $CurlOpts: extra curl opts sent in the socket
#    $XmlFile: boolean, if so it returns an XML Object
###
function httpCacheSocket($Socket = null, $Domain = null, $Uri = array(), $CacheFile = null,
    $Expiry = 60, $CurlOpts = array(), $XmlFile = 0, $Callback = null)
{

    if (empty($Socket) || !in_array($Socket, array('GET', 'POST')))
    {
        trigger_error('httpCacheSocket() called without a proper socket type.',
            E_USER_WARNING) ;
        return false ;
    }
    if (empty($Domain))
    {
        trigger_error('httpCacheSocket() called with no Domain supplied.', E_USER_WARNING) ;
        return false ;
    }
    if (empty($Uri))
    {
        trigger_error('httpCacheSocket() called with an empty Uri paramater',
            E_USER_WARNING) ;
        return false ;
    }
    if (is_array($Callback) && !is_array($Uri))
    {
        trigger_error('httpCacheSocket() recieved a callback function, but Uri was not an array',
            E_USER_WARNING) ;
    }

    # just incase CacheFile isn't an array
    if (!is_array($CacheFile))
    {
        $CacheFile = array($CacheFile) ;
    }
    if (!is_array($Uri))
    {
        $Uri = array($Uri) ;
    }

    $Urls = array() ;
    $Files = array() ;
    for ($i = 0; $i < count($CacheFile); $i++)
    {
        if (!empty($CacheFile[$i]) && false !== ($File = checkCache($CacheFile[$i], $Expiry)))
        {
            # echo 'File returned ' . chr(10) ;
            if ($Callback != null && count($Uri) > 1 && count($CacheFile) > 1)
            {
                # echo 'Cachefile(s) found for ' . $Domain . $Uri[$i] . chr(10) ;
                call_user_func($Callback, $File, $Domain . $Uri[$i]) ;
                unset($File) ;
            }
            else
            {
                # echo 'Cachefile found for ' . $Domain . $Uri[$i] . chr(10) ;
                return $File ;
            }
        }
        else
        {
            $Files[] = $CacheFile[$i] ;
            $Urls[] = $Uri[$i] ;
        }
    }
    $CacheFile = $Files ;
    $Uri = $Urls ;

    if (count($Uri) == 0)
    {
        return true ;
    }

    if ($Callback != null && count($Uri) > 1 && count($CacheFile) > 1)
    {
        return socketQueue($Domain, $Uri, $Callback, $CacheFile, false) ;
    }

    else
    {
        $CacheFile = implode('', $CacheFile) ;
        $Uri = implode('', $Uri) ;
    }

    $IpList = array(
    ) ;
    switch ($Socket)
    {
        case 'POST':
            $Ch = curl_init() ;
            $Interface = $IpList[array_rand($IpList)] ;
            if (!$Ch)
                return false ;
                
            
            curl_setopt($Ch, CURLOPT_URL, $Domain . $Uri) ;
            curl_setopt($Ch, CURLOPT_INTERFACE, $Interface) ;
            curl_setopt($Ch, CURLOPT_TIMEOUT, 20) ;
            curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1) ;
            curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true) ;
            curl_setopt($Ch, CURLOPT_POST, count($fields));
            curl_setopt($Ch, CURLOPT_POSTFIELDS,$fields_string);
        break ;
        case 'GET':
            $Ch = curl_init() ;
            $Interface = $IpList[array_rand($IpList)] ;
            if (!$Ch)
            {
                return false ;
            }
            curl_setopt($Ch, CURLOPT_URL, $Domain . $Uri) ;
            curl_setopt($Ch, CURLOPT_INTERFACE, $Interface) ;
            curl_setopt($Ch, CURLOPT_TIMEOUT, 20) ;
            curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1) ;
            curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true) ;
            curl_setopt($Ch, CURLOPT_USERAGENT,
                'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)') ;
            # if we have any curl opts, lets send them here
            if (!empty($CurlOpts))
            {
                @curl_setopt_array($Ch, $CurlOpts) ;
            }

            $File = curl_exec($Ch) ;
            if (!curl_errno($Ch))
            {
                curl_close($Ch) ;
                if ($CacheFile != null)
                {
                    cacheFile($CacheFile, $File) ;
                }
                if (is_array($Callback) && is_array($Uri))
                {
                    return call_user_func($Callback, $File, $Domain . $Uri) ;
                }
                return $File ;
            }
            else
            {
                $Obj = new stdClass ;
                $Obj->errno = curl_errno($Ch) ;
                $Obj->error = curl_error($Ch) ;
                curl_close($Ch) ;
                return $Obj ;
            }
            break ;
        case 'POST':
            
            break ;
    }
    return null ;
}

###
#    $Target: Domain name to target
#    $HttpUrl: array of URI's to retrieve off the domain'
#    $Callback: Function name to call with the page data
#    $CacheFile: Filenames if we''re caching
#    $Debug: Boolean, output debug data
#    $Max: Maximum queue length (number of active sockets)
#    $Retries: How many times to rety a socket on failure
###
function socketQueue($Target, $HttpUrl, $Callback)
{
    $Debug = true ;
    $IpList = array(
    ) ;
    $Queue = array() ;
    foreach ($HttpUrl as $Url)
    {
        $Queue[] = array(
            'Url' => $Target . $Url,
            'Started' => false,
            'Handle' => null,
            'Fails' => 1
        ) ;
    }
    $Handle = curl_multi_init() ;
    while (count($Queue) > 0)
    {
        curl_multi_exec($Handle, $Running) ;
        usleep(10000) ; 
        $Active = 0 ;
        foreach ($Queue as $Key => $Request)
        {
            if ($Active > 2)
            {
                break ;
            }
            if (!isset($Request['Fails']))
            {
                $Request['Fails'] = 0 ;
            }
            if ($Request['Started'] == false)
            { //Starts a socket
                $Request['StartTime'] = microtime(true) ;
                $Curl = curl_init($Request['Url']) ;
                $Ip = $IpList[array_rand($IpList)] ;
                curl_setopt($Curl, CURLOPT_INTERFACE, $Ip) ;
                curl_setopt($Curl, CURLOPT_TIMEOUT, 20) ;
                curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1) ;
                curl_setopt($Curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)') ;
                curl_multi_add_handle($Handle, $Curl) ;
                $Request['Started'] = true ;
                $Request['Handle'] = $Curl ;
                echo "Opened socket #{$Key} to {$Request['Url']}\n" ;
            } 
            elseif (curl_errno($Request['Handle']) != 0 || (microtime(true) - $Request['StartTime']) > 60)
            { //Socket broke
                if ($Request['Fails'] > 5)
                { //Failure!
                    echo "Socket #{$Key} Failed after " . 5 . " Attempts. Giving up.\n" ;
                    curl_multi_remove_handle($Handle, $Request['Handle']) ;
                    curl_close($Request['Handle']) ;
                    unset($Queue[$Key]) ;
                    continue ;
                }
                else
                { //Retry
                    if ($Debug)
                    {
                        echo "Socket #{$Key} Failed. Trying again.\n" ;
                    }
                    curl_multi_remove_handle($Handle, $Request['Handle']) ;
                    curl_close($Request['Handle']) ;
                    $Request['Started'] = false ;
                    $Request['Fails']++ ;
                }
            }
            else
            {
                $Data = curl_multi_getcontent($Request['Handle']) ;
                if (strpos($Data, "</html>") !== false)
                { //Socket done
                    if ($Debug)
                    {
                        echo "Successfully downloaded: " . $Request['Url']. "\n" ;
                    }
                    call_user_func($Callback, $Data, $Request['Url']) ;
                    unset($Queue[$Key]) ;
                    $Time = round(microtime(true) - $Request['StartTime'], 6) ;
                    curl_multi_remove_handle($Handle, $Request['Handle']) ;
                    curl_close($Request['Handle']) ;
                    unset($Data) ;
                    continue ;
                }
            }
            $Queue[$Key] = $Request ;
            $Active++ ;
        } //foreach
    }
    return ;
}
###
# Returns the reason for a socket error
###
function socketError($Numeric)
{
    $HttpErrors = array(100 => "Continue", 101 => "Switching Protocols", 200 => "OK",
        201 => "Created", 202 => "Accepted", 203 => "Non-Authoritative Information", 204 =>
        "No Content", 205 => "Reset Content", 206 => "Partial Content", 300 =>
        "Multiple Choices", 301 => "Moved Permanently", 302 => "Found", 303 =>
        "See Other", 304 => "Not Modified", 305 => "Use Proxy", 306 => "(Unused)", 307 =>
        "Temporary Redirect", 400 => "Bad Request", 401 => "Unauthorized", 402 =>
        "Payment Required", 403 => "Forbidden", 404 => "Not Found", 405 =>
        "Method Not Allowed", 406 => "Not Acceptable", 407 =>
        "Proxy Authentication Required", 408 => "Request Timeout", 409 => "Conflict",
        410 => "Gone", 411 => "Length Required", 412 => "Precondition Failed", 413 =>
        "Request Entity Too Large", 414 => "Request-URI Too Long", 415 =>
        "Unsupported Media Type", 416 => "Requested Range Not Satisfiable", 417 =>
        "Expectation Failed", 500 => "Internal Server Error", 501 => "Not Implemented",
        502 => "Bad Gateway", 503 => "Service Unavailable", 504 => "Gateway Timeout",
        505 => "HTTP Version Not Supported") ;
    if (isset($HttpErrors[$Numeric]))
    {
        return $HttpErrors[$Numeric] ;
    }
    else
    {
        return false ;
    }
}

function sqlSearch($DbName, $TbName, $Column, $Search, $Limit = 10)
{
    global $Dbc ;
    
    $Search = stripslashes($Search) ;
    
    if (!$Dbc->isActive())
    {
        return false ;
    }

    $Query = "
        SELECT * FROM " . $DbName . "." . $TbName . " 
        WHERE " . $Column . " = '" . $Dbc->sql_escape($Search) . "'
    " ;

    $Result = $Dbc->sql_query($Query) ;
    if ($Dbc->sql_num_rows($Result) > 0)
    {
        return $Result ;
    }

    $Search = '+' . trim(implode('* +', explode(' ', trim($Dbc->sql_escape($Search))))) . '*' ;

    $Query = "
        SELECT *, MATCH (" . $Column . ") AGAINST ('" . $Search . "') AS score 
        FROM `" . $DbName . "`.`" . $TbName . "` 
        WHERE MATCH (" . $Column . ") AGAINST ('" . $Search . "' IN BOOLEAN MODE) 
        ORDER BY score DESC " ;
    #echo $Query . chr(10) ;
    $Result = $Dbc->sql_query($Query) ;
    if ($Dbc->sql_num_rows($Result) > 0)
    {
        return $Result ;
    }

    return false ;
}

function cacheErrorHandler($Obj)
{
    if (is_object($Obj))
    {
        echo 'PHP: Socket error [' . $Obj->errno . '] occurred. Error: ' . $Obj->error .
        '.' . chr(10) ;
    }    
}

function parse_html($Src, $String, $End)
{
    $Start = strpos($Src, $String) + strlen($String) ;
    $Stop = strpos($Src, $End, $Start) - $Start ;
    $Return = trim(substr($Src, $Start, $Stop)) ;
    $Return = (empty($Return)) ? null : $Return ;
    return $Return ;
}

###
# $Words: Run the porter stemming algorithm on the words
###
function stemPhrase($Words)
{
    $Words = explode(' ', trim($Words)) ;
    for ($i = 0; $i < count($Words); $i++)
    {
        $Words[$i] = PorterStemmer::Stem($Words[$i]) ;
    }
    # var_dump($Words) ;
    return implode(' ', $Words) ;
}

###
# $String: a string based number sortened (1k 1.6m, etc)
###
function stringToNum($String)
{
    if (stristr($String, "k"))
    {
        $String = $String * 1000 ;
    } elseif (stristr($String, "m"))
    {
        $String = $String * 1000000 ;
    } elseif (stristr($String, "b"))
    {
        $String = $String * 1000000000 ;
    }
    return (int)str_replace(",", "", $String) ;
}

###
# $Num: reverse of stringToNum
###
function numToString($Num)
{
    $Num = explode(".", str_replace(",", ".", number_format($Num))) ;
    if (count($Num) == 2 && strlen(implode(".", $Num)) > 5)
    {
        return substr(implode(".", $Num), 0, -2) . "k" ;
    } elseif (count($Num) == 3)
    {
        return substr(implode(".", $Num), 0, -6) . "m" ;
    } elseif (count($Num) >= 4)
    {
        return substr(implode(".", $Num), 0, -10) . "b" ;
    }
    else
    {
        return number_format(implode("", $Num)) ;
    }
}

###
# $Exp: converts Runescape exp values to a level
###
function undoexp($Exp)
{
    $e = 0 ;
    $x = 1 ;
    while ($e <= $Exp)
    {
        $e += floor($x + 300 * pow(2, $x / 7)) / 4 ;
        $x++ ;
    }
    return $x - 1 ;
}

function statsxp($Level) {
  $x = 1; 
  $Exp = 0 ; 
  while ($x <= ($Level - 1)) { 
    $TempXp = floor(($x + 300 * pow(2, $x / 7))) / 4 ; 
    $Exp += $TempXp ; 
    $x++ ;
  }
  return (int)($Exp) ;
}

###
# $Level: converts a Runescape level to it's exp value
###
function expget($level)
{
    if ($level > 126)
    {
        return 200000000 ;
    }
    $x = 1 ;
    $Level = $level - 1 ;
    $xp = 0 ;
    while ($x <= $Level)
    {
        $TempXp = floor($x + 300 * pow(2, $x / 7)) / 4 ;
        $xp += $TempXp ;
        $x++ ;
    }
    return (int)$xp ;
}

###
# $String: value containing text with empty lines and lots of whitespace
###
function removeEmptyLines($String)
{
    return preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $String) ;
}

###
# $a: integer, returns the seconds converted to a string
###
function duration($seconds, $max_periods = 4)
{
    $periods = array("year" => 31536000, "month" => 2419200, "week" => 604800, "day" =>
        86400, "hour" => 3600, "min" => 60, "sec" => 1) ;
    $i = 1 ;
    $seconds = abs($seconds) ;
    foreach ($periods as $period => $period_seconds)
    {
        $period_duration = floor($seconds / $period_seconds) ;
        $seconds = $seconds % $period_seconds ;
        if ($period_duration == 0)
        {
            continue ;
        }
        $duration[] = $period_duration . ' ' . $period . ($period_duration > 1 ? 's' : '') ;
        $i++ ;
        if ($i > $max_periods)
        {
            break ;
        }
    }
    return implode(' ', $duration) ;
}

###
# Pretty obvious lol
###
function htmlfree($String)
{
    $String = preg_replace("/(^[^<]*>|<[^>]*>|<[^>]*$)/", '', $String) ;
    return trim(html_entity_decode(htmlentities($String))) ;
}
###
# Update user tracker info after 
# the rsn has been validated
###
function trackerUpdate($User)
{
    $Src = httpCacheSocket('GET','http://rscript.org','/lookup.php?type=track&time=0&skill=0&user=' . urlencode($User),null,0) ;
	$Return = (!is_object($Src)) ? true : false ;
    unset($Src) ;
	return $Return ;
}

###
# $Dbc: database object
# isUpdating, GE command
###
function isUpdating()
{
    global $Dbc ;
    $Query = "SELECT value FROM settings WHERE setting = 'geu_updating'" ;
    $Result = $Dbc->sql_query($Query) ;
    if (!is_resource($Result))
    {
        $Dbc->sql_freeresult($Result) ;
        return false ;
    }
    $Object = $Result->fetch_object() ;
    $Dbc->sql_freeresult($Result) ;
    return $Object ;
}

function getItemById($ID)
{
    global $Dbc ;
    $Query = "
        SELECT name, id, price, rise, members 
        FROM `Parsers`.`ge_data` 
        WHERE `id` = '" . $ID . "'
    " ;
    $Result = $Dbc->sql_query($Query) ;
    if ($Dbc->sql_num_rows($Result) == 0)
    {
        return null ;
    }
    $Obj = $Dbc->sql_fetch($Result) ;
    $Dbc->sql_freeresult($Result) ;
    return $Obj ;
}

$Skills = array() ;
$Skills[] = array('Overall', 'Attack', 'Defence', 'Strength', 'Constitution',
    'Ranged', 'Prayer', 'Magic', 'Cooking', 'Woodcutting', 'Fletching', 'Fishing',
    'Firemaking', 'Crafting', 'Smithing', 'Mining', 'Herblore', 'Agility',
    'Thieving', 'Slayer', 'Farming', 'Runecraft', 'Hunter', 'Construction',
    'Summoning', 'Dungeoneering', 'Dueling', 'Bounty', 'Bounty-Rogue', 'FOG', 'M-A',
    'BA-Attack', 'BA-Defend', 'BA-Collect', 'BA-Heal', 'CastleWars', 'Conquest') ;
$Skills[] = array('Overall', 'Attack', 'Defence', 'Strength', 'Constitution',
    'Ranged', 'Prayer', 'Magic', 'Cooking', 'Woodcutting', 'Fletching', 'Fishing',
    'Firemaking', 'Crafting', 'Smithing', 'Mining', 'Herblore', 'Agility',
    'Thieving', 'Slayer', 'Farming', 'Runecraft', 'Hunter', 'Construction',
    'Summoning', 'Dungeoneering') ;
$Skills[] = array('Overall' => 0, 'Attack' => 1, 'Defence' => 2, 'Strength' => 3,
    'Constitution' => 4, 'Ranged' => 5, 'Prayer' => 6, 'Magic' => 7, 'Cooking' => 8,
    'Woodcutting' => 9, 'Fletching' => 10, 'Fishing' => 11, 'Firemaking' => 12,
    'Crafting' => 13, 'Smithing' => 14, 'Mining' => 15, 'Herblore' => 16, 'Agility' =>
    17, 'Thieving' => 18, 'Slayer' => 19, 'Farming' => 20, 'Runecraft' => 21,
    'Hunter' => 22, 'Construction' => 23, 'Summoning' => 24, 'Dungeoneering' => 25,
    'Dueling' => 26, 'Bounty' => 27, 'Bounty-Rogue' => 28, 'FOG' => 29, 'M-A' => 30,
    'BA-Attack' => 31, 'BA-Defend' => 32, 'BA-Collect' => 33, 'BA-Heal' => 34,
    'CastleWars' => 35, 'Conquest' => 36) ;
$Skills[] = array('Overall' => 'Overall', 'Attack' => 'Atk', 'Defence' => 'Def', 'Strength' => 'Str',
    'Constitution' => 'Cons', 'Ranged' => 'Range', 'Prayer' => 'Pray', 'Magic' => 'Mage', 'Cooking' => 'Cook',
    'Woodcutting' => 'WC', 'Fletching' => 'Fletch', 'Fishing' => 'Fish', 'Firemaking' => 'FM',
    'Crafting' => 'Craft', 'Smithing' => 'Smith', 'Mining' => 'Mine', 'Herblore' => 'Herb', 'Agility' =>
    'Agil', 'Thieving' => 'Thieve', 'Slayer' => 'Slay', 'Farming' => 'Farm', 'Runecrafting' => 'RC',
    'Hunter' => 'Hunt', 'Construction' => 'Con', 'Summoning' => 'Summ', 'Dungeoneering' => 'Dung') ;

###
# Error handlers
###
function errorHandler($errno, $errstr, $errfile, $errline)
{
    #echo 'File ['.$errline.']: ' . $errfile . chr(10) ;
    switch ($errno)
    {
        case E_WARNING:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        case E_CORE_ERROR:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        case E_COMPILE_WARNING:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        case E_USER_ERROR:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        case E_USER_WARNING:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        case E_USER_NOTICE:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
        default:
            #echo 'PHP: ' . $errstr . chr(10) ;
            break ;
    }
    return true ;
}

?>