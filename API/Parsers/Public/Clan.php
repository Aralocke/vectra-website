<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
echo 'DATA: The data from this feed is pulled from Runehead.com' . chr(10) ;

$Method = 0 ;
$Search = null ;
$Sub    = Null ;
if (!empty($_GET['search']))
{
    $Search = trim(urldecode($_GET['search'])) ;
}
if (!empty($_GET['method']))
{
    $Method = trim(urldecode($_GET['method'])) ;
}
if (empty($Search))
{
    echo 'ALERT: This feed parses both clan names AND usernames.' . chr(10) ;
    echo 'ERROR: Missing arguments &search' . chr(10) ;
}
else
{
    $CacheFile = 'RuneHead.' . $Method . '.Search.' . $Search ;
    switch ((int)$Method)
    {
        case 1 :   
            # ML search & Clan compare
            $Uri = '/feeds/lowtech/searchclan.php?type=2&search=' . urlencode($Search) ;
            if (!empty($_GET['compare']))
            {
                $CompareTo = trim(urldecode($_GET['compare'])) ;
                if (!empty($_GET['exact']))
                {
                    $CompareExact = trim(urldecode($_GET['exact'])) ;
                }
            }
   
            $Src = httpCacheSocket(
                'GET', 'http://runehead.com', $Uri, $CacheFile, 120
            ) ;
            
            if (!is_object($Src))
            {
                # Parse out @@start and @@end
                $Src = substr($Src, strpos($Src, '@@start') + strlen('@@start')) ;
                $Src = substr($Src, 0, strpos($Src, '@@end')) ;
                
                if (stristr($Src, '@@Not'))
                {
                    echo 'ERROR: Search ' . str_replace(' ', '_', $Search) . ' does not exist or is not listed on runehead' . chr(10) ;
                }
                else
                {
                    $Clans = explode(chr(10), trim($Src)) ;
                    if (!empty($CompareTo))
                    {
                        $CacheFile = 'RuneHead.' . $Method . '.Search.' . $CompareTo ;
                        $Src = httpCacheSocket(
                            'GET', 
                            'http://runehead.com', '/feeds/lowtech/searchclan.php?type=2&search=' . urlencode($CompareTo),
                             $CacheFile, 120
                        ) ;
                        
                        if (!is_object($Src))
                        {
                            # Parse out @@start and @@end
                            $Src = substr($Src, strpos($Src, '@@start') + strlen('@@start')) ;
                            $Src = substr($Src, 0, strpos($Src, '@@end')) ;
                            
                            if (stristr($Src, '@@Not'))
                            {
                                echo 'ERROR: Search ' . str_replace(' ', '_', $CompareTo) . ' does not exist or is not listed on runehead' . chr(10) ;
                            }
                            else
                            {
                                # For now we take the first entry in the list of searches
                                if (!empty($_GET['num']) && (is_numeric($_GET['num']) && $_GET['num'] > 0))
                                {
                                    $Num = (int)trim($_GET['num']) ;
                                    if ($Num >= count($Clans))
                                    {
                                        $Num = count($Clans) - 1 ;
                                    }
                                }
                                else
                                {
                                    $Num = 0 ;
                                }
                                $Clan = explode('|', $Clans[$Num]) ;
                                
                                # Find other clans not being used
                                $List = array() ;
                                for ($i = 0; $i < count($Clans); $i++)
                                {
                                    if ($Num == $i) 
                                    {
                                        continue ;
                                    }
                                    $Names  = explode('|', $Clans[$i]) ;
                                    $List[] = str_replace(' ', '_', trim($Names[0])) ;
                                }
                                echo 'OTHER: ' . count($List) . ' ' . implode(' ', $List) . chr(10) ;
                                # Explode the clan searches
                                $Compare = explode(chr(10), trim($Src)) ;

                                # If exact is specifiedi search for it
                                if (!empty($CompareExact))
                                {
                                    for ($i = 0; $i < count($Compare) ; $i++)
                                    {
                                        $Link = explode('|', $Compare[$i]) ;
                                        $Link = trim(substr($Link[2], strpos($Link[2], '=') + 1)) ;
                                        if ($Link === $CompareExact)
                                        {
                                            $ClanNum = $i ;
                                            break ;
                                        }
                                    }
                                    
                                }
                                $Compare = explode('|', $Compare[((isset($ClanNum)) ? $ClanNum : 0)]) ;
                                
                                $Title = explode('|', 'name|Website|Memberlist|Type|Initials|members|Avgcombat|Avghp|Avgtotal|Avgmagic|Avgranged|base|Time|Cape|Homeworld') ;
                                for ($i = 0; $i < count($Title); $i++)
                                {
                                    if ($i == 1 || $i == 2)
                                    {
                                        echo sprintf("%s: %s %s\n",
                                            strtoupper($Title[$i]), 
                                            ((SHORT_LINKS) ? Google::shortUrl($Clan[$i]) : $Clan[$i]), 
                                            ((SHORT_LINKS) ? Google::shortUrl($Compare[$i]) : $Compare[$i])
                                        ) ;
                                    }
                                    else
                                    {
                                        echo sprintf("%s: %s %s\n", strtoupper($Title[$i]), str_replace(' ', '_', $Clan[$i]), str_replace(' ', '_', $Compare[$i])) ;
                                    }
                                }
                                $Link = 'http://runehead.com/clans/compare.php?username1='.trim(substr($Clan[2], strpos($Clan[2], '=') + 1)).'&username2='.trim(substr($Compare[2], strpos($Compare[2], '=') + 1)).'&compare=true&compareType=all' ;
                                echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link):$Link) . chr(10) ;
                            }
                        }
                    }
                    else
                    {
                        echo 'RESULTS: ' . count($Clans) . chr(10) ;
                        for ($i = 0; $i < count($Clans); $i++)
                        {   
                            $Info = explode('|', $Clans[$i]) ;
                            $Info[2] = ((SHORT_LINKS) ? Google::shortUrl($Info[2]) : $Info[2]) ;
                            echo 'CLAN: ' . implode('|', $Info) . chr(10) ;
                        }
                    }
                }
            }
            else
            {
                cacheErrorHandler($Src) ;
            }
        break ;
        case 0 :
            #User search
            $Uri = '/feeds/lowtech/searchuser.php?user=' . urlencode($Search) .'&type=2' ;    
            $Src = httpCacheSocket(
                'GET', 'http://runehead.com', $Uri, $CacheFile, 120
            ) ;
            
            if (!is_object($Src))
            {
                # Parse out @@start and @@end
                $Src = substr($Src, strpos($Src, '@@start') + strlen('@@start')) ;
                $Src = substr($Src, 0, strpos($Src, '@@end')) ;
                
                if (stristr($Src, '@@Not'))
                {
                    echo 'ERROR: Search ' . str_replace(' ', '_', $Search) . ' does not exist or is not listed on runehead' . chr(10) ;
                }
                else
                {
                    $Clans = explode(chr(10), trim($Src)) ;
                    echo 'RESULTS: ' . count($Clans) . chr(10) ;
                    for ($i = 0; $i < count($Clans); $i++)
                    {
                        echo 'CLAN: ' . $Clans[$i] . chr(10) ;
                    }
                }
            }
            else
            {
                cacheErrorHandler($Src) ;
            }
        break ;
        case 2 :
            #Clan rank
            # Options numRank, User
            if (!empty($_GET['rank']) && (is_numeric($_GET['rank']) && $_GET['rank'] > 0))
            {
                $Rank = (int)trim($_GET['rank']) ;
            } 
            elseif (!empty($_GET['user']))
            {
                $User = trim(urldecode($_GET['user'])) ;
            }
            
            if (empty($User) && empty($Rank))
            {
                echo 'ERROR: Missing arguements &rank or &user' . chr(10) ;
            }
            else
            {
                $Uri = '/feeds/lowtech/searchclan.php?type=2&search=' . urlencode($Search) ;
                $Src = httpCacheSocket(
                    'GET', 'http://runehead.com', $Uri, $CacheFile, 120
                ) ;
                
                if (!is_object($Src))
                {
                    # Parse out @@start and @@end
                    $Src = substr($Src, strpos($Src, '@@start') + strlen('@@start')) ;
                    $Src = substr($Src, 0, strpos($Src, '@@end')) ;
                    
                    if (stristr($Src, '@@Not'))
                    {
                        echo 'ERROR: Search ' . str_replace(' ', '_', $Search) . ' does not exist or is not listed on runehead' . chr(10) ;
                    }
                    else
                    {
                        $Clans = explode(chr(10), trim($Src)) ;
                        if (!empty($_GET['num']) && (is_numeric($_GET['num']) && $_GET['num'] > 0))
                        {
                            $Num = (int)trim($_GET['num']) ;
                            if ($Num > count($Clans))
                            {
                                $Num = count($Clans) - 1 ;
                            }
                        }
                        else
                        {
                            $Num = 0 ;
                        }
                        if (!empty($_GET['exact']))
                        {
                            for ($i = 0; $i < count($Clans) ; $i++)
                            {
                                $Link = explode('|', $Clans[$i]) ;
                                $Link = trim(substr($Link[2], strpos($Link[2], '=') + 1)) ;
                                if ($Link === trim($_GET['exact']))
                                {
                                    $ClanNum = $i ;
                                    break ;
                                }
                            }                            
                        }
                        $Clan = explode('|', $Clans[((isset($ClanNum)) ? $ClanNum : $Num)]) ;
                        
                        # Find other clans not being used
                        $List = array() ;
                        for ($i = 0; $i < count($Clans); $i++)
                        {
                            if ($Num == $i) 
                            {
                                continue ;
                            }
                            $Names  = explode('|', $Clans[$i]) ;
                            $List[] = str_replace(' ', '_', trim($Names[0])) ;
                        }
                        echo 'OTHER: ' . count($List) . ' ' . implode(' ', $List) . chr(10) ;
                        $Link = $Clan[2] ;
                        echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Clan[2]) : $Clan[2]) . chr(10) ;
                        
                        $CacheFile = 'ClanRank.'.trim(substr($Link[2], strpos($Link[2], '=') + 1)) ;
                        $Src = httpCacheSocket('GET', 'http://runehead.com', trim(substr($Link, strpos($Link, '.com') + 4)), $CacheFile, 120) ;

                        if (!is_object($Src))
                        {
                            $Pattern = "#<td>(\d+)</td>[\r\n\s]+<td title='.*'><a href='.*' style='.*'>(.*)</a></td>[\r\n\s]+<td title='.*' style='.*'>(.*)</td>[\r\n\s]+<td title='.*' style='.*'>(.*)</td>[\r\n\s]+<td title='.*' style='.*'>(.*)</td>[\r\n\s]+<td title='.*' style='.*'>(.*)</td>#i" ;
                            preg_match_all($Pattern, $Src, $Match) ;
                            if (empty($Match))
                            {
                                echo 'PHP: Error parsing page. Data not found' . chr(10) ; 
                            }
                            else
                            {
                                $Ranks   = $Match[1] ;
                                $RSNs    = $Match[2] ;
                                $Combat  = $Match[3] ;
                                $HP      = $Match[4] ;
                                $Overall = $Match[5] ;
                                $Top     = $Match[6] ;
                                
                                $Members = count($Ranks) ;
                                
                                if (!empty($Rank))
                                {
                                    $UserNum = (($Rank - 1) > ($Members - 1)) ? ($Members - 1) : ($Rank - 1) ;
                                }
                                elseif (!empty($User))
                                {
                                    for ($i = 0; $i < $Members; $i++)
                                    {
                                        if ($RSNs[$i] == $User)
                                        {
                                            $UserNum = $i ;
                                            break ;
                                        }
                                    } 
                                }
                                if (!isset($UserNum))
                                {
                                    echo 'ERROR: User '.$User.' was not found in the '.$Clan[0].' clan' . chr(10) ;
                                }
                                else
                                {
                                    echo 'MEMBERS: ' . $Members . chr(10) ;
                                    echo 'RANK: ' . $Ranks[$UserNum] . chr(10) ;
                                    echo 'RSN: ' . $RSNs[$UserNum] . chr(10) ;
                                    echo 'COMBAT: ' . $Combat[$UserNum] . chr(10) ;
                                    echo 'HP: ' . $HP[$UserNum] . chr(10) ;
                                    echo 'OVERALL: ' . $Overall[$UserNum] . chr(10) ;
                                    echo 'HIGHLEVEL: ' . $Top[$UserNum] . chr(10) ;
			                    }
                            }
                        }
                        else
                        {
                            cacheErrorHandler($Src) ;
                        }
                    }
                }
                else
                {
                    cacheErrorHandler($Src) ;
                }
            }
        break ;
        default :
            if (empty($Method))
            {
                echo 'ERROR: Missing arguments &method' . chr(10) ;
            }
            else
            {
                echo 'ERROR: Unknown &method given' . chr(10) ;
            }
        break ;
    }
}

?>