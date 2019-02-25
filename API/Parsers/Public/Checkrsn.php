<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

define ('USER_NAME', 'vectra@vectra-bot.net') ;
define ('PASSWORD', '') ;

if (empty($_GET["rsn"]))
{
    print 'ERROR: Missing argument &rsn' . chr(10) ;
}
else
{
    $Rsn = urldecode($_GET['rsn']) ;
    if (strlen($Rsn) > 12 or !preg_match("@^[-a-z0-9_ ]+$@i", $Rsn))
    {
        print 'ERROR: Username must be no longer then 12 chars and alphanumeric' . chr(10) ;
    }
    else
    {
       $Domain = 'https://secure.runescape.com/m=weblogin/login.ws' ;
       $Post   = array (
            'username' => USER_NAME,
            'password' => PASSWORD,
            'rem'      => 0,
            'mod'      => 'displaynames',
            'ssl'      => 1, 
            'dest'     => 'check_name.ws?displayname=' . $Rsn
       ) ;
       $Result = postData($Domain, $Post) ;
       $Result = explode(chr(10), trim($Result)) ;
       
       if ($Result[0] == 'NOK')
       {
            echo 'NAMECHECK: NOTAVAILIBLE' .chr(10) ;
            $Suggestions = array() ;
            for ($i = 1; $i < count($Result); $i++)
            {
                if (!empty($Result[$i]))
                {
                    $Suggestions[] = $Result[$i] ;
                }
            }
            if (!empty($Suggestions))
            {
                $Suggestions = implode(', ', $Suggestions) ;
                echo 'SUGGESTION: ' . str_replace('&nbsp;', '_', htmlentities($Suggestions)) . chr(10) ;
            }
       }
       else { echo 'NAMECHECK: AVAILIBLE' . chr(10) ; }
    }
}
function postData ($PostUrl, $PostData)
{ 
    $login_url = 'https://secure.runescape.com/m=weblogin/loginform.ws';
    $login_post_url = 'https://secure.runescape.com/m=weblogin/login.ws'; 
    $Agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)';
    
    
    $Ch = curl_init() ;
    if (!$Ch)
    {
        return false ;
    }
    
    curl_setopt($Ch, CURLOPT_URL, $login_post_url); 
    curl_setopt($Ch, CURLOPT_INTERFACE, '69.147.235.196') ;    
    curl_setopt($Ch, CURLOPT_POST, 1) ;
    curl_setopt($Ch, CURLOPT_POSTFIELDS, http_build_query($PostData)) ;
    curl_setopt($Ch, CURLOPT_REFERER, $login_url);    
    curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1) ;
    curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, 1) ;
    curl_setopt($Ch, CURLOPT_FRESH_CONNECT, 1) ;
    curl_setopt($Ch, CURLOPT_USERAGENT, $Agent) ;
    curl_setopt($Ch, CURLOPT_SSL_VERIFYHOST,  2);
    curl_setopt($Ch, CURLOPT_SSL_VERIFYPEER, false);
    $File = curl_exec($Ch) ;
    if (!curl_errno($Ch))
    {
        #$Info = curl_getinfo($Ch) ;
        curl_close($Ch) ;
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
}
?>