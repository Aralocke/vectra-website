<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

function parseXML ($doc)
{
    if (is_object($doc))
        return $doc ;
    $obj = @simplexml_load_string($doc) ;  
    return $obj ;
}

$Rsn = '' ;
if (!empty($_GET['rsn']))
{
    $Rsn = urldecode(trim($_GET['rsn'])) ;
}

if (!empty($Rsn))
{
    # Alog is a bit special, it's a POST but it's a GET'
    $Request = 'searchName=' . urlencode($Rsn) ;
    $CurlOpts = array(
        CURLOPT_HEADER => 0,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $Request
    ) ;

    $CacheFile = 'Alog.' . str_replace(' ', '_', $Rsn) ;
    
    $Src = httpCacheSocket(
        'GET',
        'http://services.runescape.com',
        '/m=adventurers-log/rssfeed',
        $CacheFile, 
        300, /* Jagex are butt heads - cache for 5 minutes */
        $CurlOpts
    ) ;

    if (!is_object($Src))
    {
        # Socket succeeded
        if (stristr($Src, "404 - Page not found"))
        {
            print "ERROR: The name is hidden or does not exist\n" ;
        } 
        elseif ($Src)
        {
            
            $xmldoc = parseXML($Src) ;
            
            $xmldoc = $xmldoc->channel->item ;
            if (empty($xmldoc))
            {
                print "ERROR: The name is hidden or does not exist\n" ;
            }

            if (!isset($_GET['switch']) || empty($_GET['switch'])) 
            { $Type = 'all' ; } 
            elseif ($_GET['switch'] == 'r') { $Type = 'Recent' ; } 
            elseif ($_GET['switch'] == 'k') { $Type = 'Killed' ; } 
            elseif ($_GET['switch'] == 'l') { $Type = 'Levels' ; } 
            elseif ($_GET['switch'] == 'i') { $Type = 'Items' ; } 
            elseif ($_GET['switch'] == 'q') { $Type = 'Quests' ; } 
            elseif ($_GET['switch'] == 'e') { $Type = 'Exp' ; } 
            elseif ($_GET['switch'] == 'm') { $Type = 'Misc' ; } 
            elseif ($_GET['switch'] == 't') { $Type = 'Trails' ; }
            else { $_Type = 'all' ; }

            $Alog = array() ;

            for ($Offset = 0; $Offset < count($xmldoc); $Offset++)
            {
                if (empty($xmldoc[$Offset]->title))
                {
                    continue ;
                }
                
                $Description = str_replace(array("\r", "\n"), null, trim($xmldoc[$Offset]->description)) ;
                $Description = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $Description) ;
                $Description = preg_replace("#(\s{2,})#i", " ", $Description) ;

                $Title = trim($xmldoc[$Offset]->title) ;
                $Title = str_replace(
                    array('.', '&apos'), '', 
                    html_entity_decode(
                        $Title,
                        ENT_NOQUOTES, 
                        'UTF-8'
                    )
                ) ;
                $Title = str_replace('  ', ' ', $Title) ;
                $RealTitle = $Title ;
                $Title = stemPhrase($Title) ;
                
                # Initial parsing of title for matching $Case
                $Title = str_replace(
                    array('killed'),
                    array('kill '),
                    $Title
                ) ;
                
                
                $Date = trim($xmldoc[$Offset]->pubDate) ;
                # echo $Date . ': ' . strtotime($Date, time()) . chr(10) ;
                # echo $Title .chr(10) ;
                # echo $Description . chr(10) ;
                # echo str_repeat('_', 100) . chr(10);
                
                if ($Type == 'Recent')
                {
                    $Date = explode(' ', $Date) ;
                    echo 'ALOG: ' . sprintf("%s-%s-%s", $Date[1], $Date[2], $Date[3]) . ' ' . $RealTitle . chr(10) ;
                    if ($Offset >= 5) { break ; }
                } //isset recent
                else
                {
                    $Case = '' ;
                    if (preg_match('@^(\d+)XP in (\S+)$@i', $Title, $Match)) { $Case = 'Exp' ; } 
                    elseif (stristr($Title, 'I kill')) { $Case = 'Killed' ; } 
                    elseif (stristr($Title, 'Level up')) { $Case = 'Levels' ; } 
                    elseif (stristr($Title, 'Item found:')) { $Case = 'Items' ; } 
                    elseif (stristr($Title, 'Quest complete:')) { $Case = 'Quests' ; } 
                    else { $Case = 'Misc' ; }

                    if ($Type == 'all' || $Type == $Case)
                    {
                        $String = explode(' ', $Title) ;
                        $Dstring = explode(' ', $Description) ;
                        
                        #var_dump($Title, $String, $Dstring) ;
                        
                        if ($Case == 'Killed')
                        {
                            if ($String[2] === 'null')
                            { 
                                /* Fix jagex's mistakes */
                                continue ;
                            }
                            
                            $FuckJagex = array(
                                'TzTok_Jadz' => 'TzTok_Jad'
                            ) ;
                            
                            if ($String[2] == 'a' || $String[2] == 'the')
                            {
                                $Kill = trim(
                                    substr($Title, 
                                        strpos($Title,
                                            $String[2]) + strlen($String[2])
                                        )
                                    ) ;
                                #$Kill = str_replace(' ', '_', $Kill) ;
                                $Kill = preg_replace("/[^A-Za-z09]/i", '_', trim($Kill)) ;
                                #var_dump(1, $Kill, $Title, $Description, $Date) ;
                                if (isset($FuckJagex[$Kill]))
                                {
                                    $Kill = $FuckJagex[$Kill] ;
                                }
                                if (empty($Alog[$Case][$Kill])) { $Alog[$Case][$Kill] = 1 ; }
                                else { $Alog[$Case][$Kill]++ ; }
                            } 
                            elseif (is_numeric($String[2]))
                            { 
                                $Kill = trim(
                                    substr($Title,
                                        strpos($Title,
                                            $String[2]) + strlen($String[2])
                                        )
                                    ) ;
                                #$Kill = str_replace(' ', '_', $Kill) ;
                                $Kill = preg_replace("/[^A-Za-z09]/i", '_', trim($Kill)) ;
                                #var_dump(2, $Kill, $Title, $Description, $Date) ;
                                if (isset($FuckJagex[$Kill]))
                                {
                                    $Kill = $FuckJagex[$Kill] ;
                                }
                                if (empty($Alog[$Case][$Kill])) { $Alog[$Case][$Kill] = (int)$String[2] ; }
                                else { $Alog[$Case][$Kill] += (int)$String[2] ; }
                            }
                            else
                            {
                                $Kill = substr($Title, strpos($Title, 'killed') + 6) ;
                                $Kill = preg_replace("/[^A-Za-z09]/i", '_', trim($Kill)) ;
                                #var_dump(3, $Kill, $Title, $Description, $Date) ;
                                if (isset($FuckJagex[$Kill]))
                                {
                                    $Kill = $FuckJagex[$Kill] ;
                                }
                                if (empty($Alog[$Case][$Kill])) { $Alog[$Case][$Kill] = 1 ; }
                                else { $Alog[$Case][$Kill]++ ; }
                            }
                            unset($Kill) ;
                            continue ;
                        }
                        
                        
                        elseif ($Case == 'Levels')
                        { 
                            if (is_numeric($Dstring[3]))
                            {
                                $Skill = explode(' ', $RealTitle) ;
                                $Skill = trim($Skill[count($Skill) - 1]) ;
                                $Level = trim($Dstring[3]) ;
                            }
                            else
                            {
                                $Skill = explode(' ', $RealTitle) ;
                                $Skill = trim($Skill[count($Skill) - 1]) ;
                                $Level = (int)trim(substr($Dstring[9], 0 ,-1)) ;
                            }   
                            $Skill = $Skills[3][$Skill]  ;                        
                            if (empty($Alog[$Case][$Skill]))
                            {
                                $Alog[$Case][$Skill] = array(
                                    'High' => $Level,
                                    'Low' => $Level - 1
                                ) ;
                            }
                            else
                            {
                                $Current = $Alog[$Case][$Skill] ;
                                if ($Level > $Current['High'])
                                {
                                    $Alog[$Case][$Skill]['High'] = $Level ;
                                    if ($Level < $Current['Low'])
                                    {
                                        $Alog[$Case][$Skill]['Low'] = $Current['Low'] ;
                                    }
                                } //current > level
                                elseif ($Level < $Current['Low'])
                                {
                                    $Alog[$Case][$Skill]['Low'] = $Level ;
                                }
                            }
                            unset($Skill, $Current) ;
                            continue ;
                        } 
                        
                        elseif ($Case == 'Items')
                        {
                            # echo $Title . chr(10) ;                            
                            $Item = trim(substr($Title, strpos($Title, ':') + 1)) ; 
                            if ($Item === 'null')
                            { 
                                /* Fix jagex's mistakes */
                                continue ;
                            }
                            $Prefix = array('a', 'an', 'some') ;                                                         
                            if (in_array($String[2], $Prefix))
                            {
                                $Item = trim(substr($Item, strpos($Item, ' '))) ;
                            }
                            $Item = str_replace(' ', '_', $Item) ;
                            if (empty($Alog[$Case][$Item]))
                            {
                                $Alog[$Case][$Item] = 1 ;
                            }
                            else
                            {
                                $Alog[$Case][$Item]++ ;
                            }
                            continue ;
                        }
                        
                        elseif ($Case == 'Exp')
                        {
                            # echo $Title . chr(10) ;
                            $Skill = $Skills[3][$Dstring[10]] ;
                            $Exp = (int)$Match[1] ;
                            if (empty($Alog[$Case][$Skill]["High"]))
                            {
                                $Alog[$Case][$Skill]["High"] = $Exp ;
                            }
                            else
                            {
                                if ($Exp > $Alog[$Case][$Skill]["High"])
                                {
                                    $Alog[$Case][$Skill]["High"] = $Exp ;
                                } //if
                            }
                            if (empty($Alog[$Case][$Skill]["Low"]))
                            {
                                $Alog[$Case][$Skill]["Low"] = $Exp ;
                            }
                            else
                            {
                                if ($Exp < $Alog[$Case][$Skill]["Low"])
                                {
                                    $Alog[$Case][$Skill]["Low"] = $Exp ;
                                } 
                            } 
                            continue ;
                        } 
                        
                        elseif ($Case == 'Quests')
                        {
                            $quest_name = trim(substr($RealTitle, strpos($RealTitle, ':') + 1)) ;
                            if ($quest_name === 'null' || empty($quest_name))
                            { 
                                /* Fix jagex's mistakes */
                                continue ;
                            }
                            $Alog[$Case][] = $quest_name ;
                            continue ;
                        }
                        
                        elseif ($Case == 'Misc')
                        {
                            #echo $Title . chr(10) ;
                            #var_dump($String) ;
                            if ($String[0] === 'null')
                            { 
                                /* Fix jagex's mistakes */
                                continue ;
                            }
                            if ($String[0] == 'Level')
                            { 
                                if (empty($Alog[$Case]['Levelled']))
                                {
                                    $Alog[$Case]['Levelled'] = 'All skills at least level '.$Dstring[11].'.' ;
                                }                                
                            } //levelled 
                            elseif ($String[0] == 'Dungeon')
                            { 
                                if (empty($Alog[$Case]['Dungeon']))
                                {
                                    $Alog[$Case]['Dungeon'] = 'Opened Dungeon '.$Dstring[4].'.' ;
                                }                                
                            } //Dungeon 
                            elseif ($String[0] == 'Daemonheim' || $String[0] == 'Daemonheim;' || $String[0] == "Daemonheim'")
                            { 
                                #var_dump($String) ;
                                if ($String[3] === 'null')
                                { 
                                    /* Fix jagex's mistakes */
                                    continue ;
                                }
                                elseif (empty($Alog[$Case]['Daemonheim']))
                                {
                                    $Alog[$Case]['Daemonheim'] = (int)$String[3] ;
                                }  
                                else { $Alog[$Case]['Daemonheim'] += (int)$String[3] ; }                              
                            } //Daemonheim
                            elseif ($String[2] == 'trail') 
                            {
                                if (!empty($Alog[$Case]['Trails'][$String[0]])) { $Alog[$Case]['Trails'][$String[0]]++ ; }
                                else { $Alog[$Case]['Trails'][$String[0]] = 1 ; }
                            } 
                            elseif ((!empty($String[3]) && $String[3] == 'case') || $String[2] == 'case')
                            {   # var_dump($String) ;
                                $Num = ($String[3] == 'case') ? 6 : 5 ;
                                if (!empty($Alog[$Case]['Case'][$String[$Num]])) { $Alog[$Case]['Case'][$String[$Num]]++ ; }
                                else { $Alog[$Case]['Case'][$String[$Num]] = 1 ; }
                            }
                            elseif ($String[2] == 'challeng')
                            {
                                if (!empty($Alog[$Case]['Champion'][$Dstring[15]])) { $Alog[$Case]['Champion'][$Dstring[15]]++ ; }
                                else { $Alog[$Case]['Champion'][$Dstring[15]] = 1 ; }
                            }                           
                            else { $Alog[$Case][] = $RealTitle . '.' ; }  
                            # var_dump($Dstring) ;
                        }                
                           
                        unset($Title, $Description, $Date, $Case, $Match) ;                    
                    } # Parsing the XML in here
                }
             }
            
            if (!empty($Alog))
            {
                $Dbc->connect() ;
                $Stemmed = array(
                /* Items */
                'abyss whip' => 'Abyssal Whip',
                'amulet of rang' => 'Range Amulet',
                'ancient effigi' => 'Ancient Effigy',
                'bando chestplat' => 'Bandos Chestplate',
                'bando hilt' => 'Bandos Hilt',
                'bando tasset' => 'Bandos Tasset',
                'dragon claw' => 'Dragon Claws',
                'dracon visag' => 'Draconic Visage',
                'dragon shield left half' => 'Shield Left Half', 
                'dragon plateleg' => 'Dragon Platelegs',               
                'granit helm' => 'Granite Helm',  
                'granit leg' => 'Granite Legs',  
                'granit maul' => 'Granite Maul', 
                'focu sight' => 'Focus Sight',
                'pair of dragon boot' => 'Dragon Boots',
                'seercul' => 'Seercull',           
                /* Monsters */
                'boss monster in daemonheim' => 'Daemonheim Bosses', 
                'dagannoth suprem' => 'Dagannoth Supreme',
                'gener graardor' => 'General Graardor',
                'skelet horror' => 'Skeletal Horror',
                'torment demon' => 'Tormented Demon'
                ) ;
                #var_dump($Alog) ;
                if (!empty($Alog['Killed']))
                {
                    print "ALOG: Killed " ;
                    $String = array() ;
                    asort($Alog['Killed']) ;
                    $Alog['Killed'] = array_reverse($Alog['Killed']) ;
                    foreach ($Alog['Killed'] as $Key => $Value)
                    {
                        $Npc = strtolower(trim(str_replace('_', ' ', $Key))) ;
                        $Npc = (isset($Stemmed[$Npc])) ? $Stemmed[$Npc] : $Npc ;
                        $String[] = "{$Value} " . ucwords($Npc) ;
                    } //foreach
                    print implode(', ', $String) . "\n" ;
                } //if killed
                if (!empty($Alog['Levels']))
                {
                    print "ALOG: Gained " ;
                    $String = array() ;
                    $Count = 0 ;
                    $Total = 0 ;
                    foreach ($Alog['Levels'] as $Skill => $Value)
                    {
                        $Exp = expget($Alog['Levels'][$Skill]['High']) - expget($Alog['Levels'][$Skill]['Low']) ;
                        $Total += (int)($Exp) ;
                        $Count += $Alog['Levels'][$Skill]['High'] - $Alog['Levels'][$Skill]['Low'] ;
                        $Exp = numToString($Exp) ;
                        $String[] = "{$Skill} {$Alog['Levels'][$Skill]['Low']}->{$Alog['Levels'][$Skill]['High']} +{$Exp}" ;
                    } //foreach
                    $Total = numToString($Total) ;
                    print "{$Count} [+{$Total}] level(s): " . implode(', ', $String) . "\n" ;
                } //if levels
            }
            if (!empty($Alog['Items']))
            {                
                $String = array() ;
                $Cash = 0 ;
                foreach ($Alog['Items'] as $Key => $Value)
                {
                    $Item = strtolower(trim(str_replace('_', ' ', $Key))) ;
                    $Item = (isset($Stemmed[$Item])) ? $Stemmed[$Item] : $Item ;
                    $String[] = "{$Value} " . ucwords(str_replace('_', ' ', $Item)) ;
                    $Search = str_replace('_', '%%', $Item) ;
                    $Result = sqlSearch('Parsers', 'ge_data', 'name', $Search) ;
                    if (!is_bool($Result))
                    {
                        $Obj = $Dbc->sql_fetch($Result) ;
                        $Cash += ($Obj->price * $Value) ;
                        $Dbc->sql_freeresult($Query) ;
                    }
                } //foreach
                print "ALOG: Item(s) " ;
                if ($Cash > 0)
                {
                    $Cash = str_replace(array('k', 'm', 'b'), array('K', 'M', 'B'), numToString($Cash)) ;
                    print "found ~{$Cash}gp: " . implode(', ', $String) . "\n" ;
                }
                else
                {
                    print "found: " . implode(', ', $String) . "\n" ;
                }
            } //if Items
            if (!empty($Alog['Exp']))
            {
                $String = array() ;
                print "ALOG: Reached " ;
                foreach ($Alog['Exp'] as $Skill => $Value)
                {
                    if ($Value["Low"] < $Value["High"])
                    {
                        $Low = numToString($Value['Low']) ;
                        $High = numToString($Value['High']) ;
                        $String[] = "{$Low}->{$High} {$Skill} exp" ;
                    }
                    else
                    {
                        $High = numToString($Value['High']) ;
                        $String[] = "{$High} {$Skill} exp" ;
                    } //else
                } //foreach
                print implode(', ', $String) . "\n" ;
            } //if exp
            if (!empty($Alog['Quests']))
            {
                $QPs = 0 ;
                foreach ($Alog['Quests'] as $Quest)
                {
                    $Query = "
                        SELECT qps 
                        FROM `Parsers`.`quest` 
                        WHERE `name` 
                        LIKE '%".$Dbc->sql_escape($Quest)."%'
                        LIMIT 1
                    " ;
                    
                    $Result = $Dbc->sql_query($Query) ;
                    if ($Dbc->sql_num_rows($Result) > 0)
                    {
                        $Obj = $Dbc->sql_fetch($Result) ;
                        $QPs += (int)$Obj->qps ;
                        $Dbc->sql_freeresult($Result) ;
                    }
                }
                print "ALOG: Completed " . count($Alog['Quests']) . " quest(s) [+".$QPs."qps]: " . implode(', ', $Alog['Quests']) . "\n" ;
            } //if quests
            if (!empty($Alog['Misc']))
            {
                print "ALOG: " ;
                foreach ($Alog['Misc'] as $Opt => $Value)
                {
                    if ($Opt == 'Trails')
                    {
                        if (is_array($Value))
                        {
                            echo 'Treasure trails: ' ;
                            $Trails = array() ;
                            foreach ($Value as $Trail => $Amt)
                            { $Trails[] = $Amt . ' ' . (($Trail == 'Easi')?'Easy':$Trail) ; }
                            echo implode(', ', $Trails) . '. ' ;
                        }
                        else { echo $Value . " " ; }
                    }
                    elseif ($Opt == 'Daemonheim')
                    {
                        echo 'Recovered ' . $Value . ' Daemonheim Volumes. ' ;
                    }
                    elseif ($Opt == 'Champion')
                    {
                        if (is_array($Value))
                        {
                            echo 'Challenges: ' ;
                            foreach ($Value as $Champion => $Amt)
                            { echo $Amt . ' ' . $Champion ; }
                            echo '. ' ;
                        }
                        else { echo $Value . " " ; }
                    }
                    elseif ($Opt == 'Case')
                    {
                        if (is_array($Value))
                        {
                            echo 'Cases: ' ;
                            foreach ($Value as $Case => $Amt)
                            { echo $Amt . ' ' . $Case ; }
                            echo '. ' ;
                        }
                        else { echo $Value . " " ; }
                    }
                    else
                    {
                        echo $Value . " " ;
                    }
                }
                echo "\n" ;
            } //if Misc
        }
    }
    else
    {
        # Socket failed
        cacheErrorHandler($Src) ;
    }
}
else
{
    echo 'ERROR: Missing argument &rsn' . chr(10) ;
}

?>