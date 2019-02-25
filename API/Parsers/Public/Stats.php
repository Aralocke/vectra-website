<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

define('STATS_RANK', 1) ;
define('STATS_LEVEL', 2) ;
define('STATS_EXP', 3) ;
define('STATS_ALL', 4) ;
$F2p = array('Attack' => 1, 'Defence' => 2, 'Strength' => 3,
    'Constitution' => 4, 'Ranged' => 5, 'Prayer' => 6, 'Magic' => 7, 'Cooking' => 8,
    'Woodcutting' => 9, 'Fishing' => 11, 'Firemaking' => 12,
    'Smithing' => 14, 'Mining' => 15, 'Runecraft' => 21, 'Dungeoneering' => 25,
    'Dueling' => 26, 'Bounty' => 27, 'Bounty-Rogue' => 28, 'FOG' => 29, 'Conquest' => 36) ;

$CompareTo = '' ;
if (!empty($_GET['compare']))
{
    if (strlen($_GET['compare']) < 13)
    {
        $CompareTo = trim(urldecode($_GET['compare'])) ;
    }
}

$Rsn = '' ;
if (!empty($_GET['rsn']))
{
    $Rsn = trim(urldecode($_GET['rsn'])) ;
}

$Filename = 'Stats.' . $Rsn ;

if (empty($Rsn))
{
    echo 'ERROR: Missing arguement &rsn.' . chr(10) ;
} elseif (strlen($Rsn) > 12)
{
    echo 'ERROR: &rsn must be 12 or fewer characters.' . chr(10) ;
}
else
{
    $Src = httpCacheSocket(
        'GET',
        'http://hiscore.runescape.com',
        '/index_lite.ws?player=' . urlencode($Rsn),
        $Filename,
        60
    ) ;
    
    $Compare = '' ;
    if (!empty($CompareTo))
    {
        $Compare = httpCacheSocket(
            'GET',
            'http://hiscore.runescape.com',
            '/index_lite.ws?player=' . urlencode($CompareTo),
            'Stats.'.$CompareTo,
            60
        ) ;        
        
    }

    if (!is_object($Src) && !is_object($Compare))
    {
        # Socket succeeded
        if (strpos($Src, "404 - Page not found"))
        {
            echo 'ERROR: User ' . $Rsn . ' was not found on the highscores.' . chr(10) ;
        }
        elseif (!empty($Compare) && strpos($Compare, "404 - Page not found"))
        {
            echo 'ERROR: User ' . $CompareTo . ' was not found on the highscores.' . chr(10) ;
        }
        else
        {
            $Mode = STATS_ALL ;
            if (!empty($_GET['mode']))
            {
                switch ($_GET['mode'])
                {
                    case STATS_RANK:
                        $Mode = 1 ;
                        break ;
                    case STATS_LEVEL:
                        $Mode = 2 ;
                        break ;
                    case STATS_EXP:
                        $Mode = 3 ;
                        break ;
                }
            }
            #Run time vars for detecting a missing skill

            $Data = generateStats($Src) ;
            if (empty($Data->Stats))
            {
                echo ' ERROR: Player ' . $Rsn . ' has no ranked skills' . chr(10) ;
            }
            else
            {
                if (empty($CompareTo))
                {
                    $CombatLevel = 0 ;
                    $Combat = trim(cmbformula($Data->Levels)) ;
                    echo 'COMBATP2P: ' . $Combat . chr(10) ;
                    echo 'COMBATF2P: ' . cmbformula($Data->Levels, 1) . chr(10) ;
                    if (isset($_GET['cmb']) && is_numeric($_GET['cmb']) && (int)($_GET['cmb']) <= 138 && (int)($_GET['cmb']) >= 4 && $CombatLevel < (int)($_GET['cmb']))
                    {
                        $NextCmb = (int)(trim($_GET['cmb'])) ;                
                        $Nextcmb = nextcmb($Data->Levels, $CombatLevel, true) ;                
                        for ($i = 1; floor($Nextcmb['Combat']) < (floor($NextCmb) - 1) ; $i++)
                        {
                            $Nextcmb = nextcmb($Nextcmb, $CombatLevel + $i, true) ;
                        }
                        $Nextcmb = nextcmb($Nextcmb, (floor($NextCmb) - 1), true) ;
                        $List = array('Attack', 'Defence', 'Strength', 'Constitution', 'Prayer', 'Magic', 'Ranged', 'Summoning') ;
                        $NextArray = array() ;
                        foreach ($List as $Skill)
                        {
                            $Update = $Nextcmb[$Skill] - $Data->Levels[$Skill] ;
                            if ($Update > 0)
                            {
                                $NextArray[] = $Skill . ' ' . $Update ;
                            }
                        }
                        echo 'NEXTCMB: ' . implode(' | ', $NextArray) . chr(10) ;
                    }
                    else
                    {
                        echo 'NEXTCMB: ' . nextcmb($Data->Levels, $CombatLevel) . chr(10) ;
                    }
                    echo 'CMBEXP: ' . $Data->totals['cmbExp'] . chr(10) ;
                    echo 'SKILLEXP: ' . $Data->totals['skillExp'] . chr(10) ;
                    echo 'F2PEXP: ' . $Data->totals['f2pExp'] . chr(10) ;
                    echo 'P2PEXP: ' . $Data->totals['p2pExp'] . chr(10) ;
                    echo 'LOWEST: ' . undoexp($Data->data['lowest']) . ' ' . $Data->data['lowest'] . chr(10) ;
                    echo 'HIGHEST: ' . undoexp($Data->data['highest']) . ' ' . $Data->data['highest'] . chr(10) ;
                    if ($Data->Overall['level'] != '0')
                    {
                        echo 'STAT: Overall ' . implode(' ', $Data->Overall) . chr(10) ;
                    }
                    foreach ($Data->Stats as $Obj) 
                    {                    
                        if (isset($_GET['ftp']) && !$Obj->f2p) { continue ; }
                        elseif (isset($_GET['ptp']) && $Obj->f2p) { continue ; }
                        $Obj->calcTog($Data->data['lowest']) ;
                        echo 'STAT: ' . $Obj->toString($Mode) . chr(10) ;
                    }
                }
                else
                {
                    $Compare = generateStats($Compare) ;
                    if (empty($Compare->Stats))
                    {
                        echo ' ERROR: Player ' . $CompareTo . ' has no ranked skills' . chr(10) ;
                    }
                    else
                    {
                        echo 'USERS: ' . str_replace(' ', '_', $Rsn) . ' ' . str_replace(' ', '_', $CompareTo) . chr(10) ;
                        $CombatLevel = 0 ;
                        echo 'COMBAT: ' . cmbformula($Data->Levels) . ' ' . cmbformula($Compare->Levels) . chr(10) ;
                        echo 'CMBEXP: ' . $Data->totals['cmbExp'] . ' ' . $Compare->totals['cmbExp'] . chr(10) ;
                        echo 'SKILLEXP: ' . $Data->totals['skillExp'] . ' ' . $Compare->totals['skillExp'] . chr(10) ;
                        echo 'F2PEXP: ' . $Data->totals['f2pExp'] . ' ' . $Compare->totals['f2pExp'] . chr(10) ;
                        echo 'P2PEXP: ' . $Data->totals['p2pExp'] . ' ' . $Compare->totals['p2pExp'] . chr(10) ;
                        if ($Data->Overall['exp'] > 0 && $Compare->Overall['exp'] > 0)
                        {
                            echo 'COMPARE: Overall ' . sprintf("%d %d %d %d", $Data->Overall['level'], $Data->Overall['exp'],
                                $Compare->Overall['level'], $Compare->Overall['exp']) . chr(10) ;
                        }
                        #var_dump($Data->Stats) ;
                        #var_dump($Compare->Stats) ;
                        for ($i = 0; $i < count($Skills[0]); $i++)
                        {
                            $Skill = $Skills[0][$i] ;
                            # Compare stats
                            if (!isset($Data->Stats[$Skill]))
                            {
                                continue ;
                            }
                            if (!isset($Compare->Stats[$Skill]))
                            {
                                continue ;
                            }
                            if ($i < 26)
                            {
                                echo 'COMPARE: ' . sprintf("%s %d %d %d %d", $Data->Stats[$Skill]->skill, $Data->Stats[$Skill]->level, 
                                    $Data->Stats[$Skill]->exp, $Compare->Stats[$Skill]->level, $Compare->Stats[$Skill]->exp) . chr(10) ;
                            }
                            else
                            {
                                echo 'COMPARE: ' . sprintf("%s %d %d %d %d", $Data->Stats[$Skill]->skill, $Data->Stats[$Skill]->rank, 
                                    $Data->Stats[$Skill]->level, $Compare->Stats[$Skill]->rank, $Compare->Stats[$Skill]->level) . chr(10) ;
                            }
                            # Compare Stats
                        }
                    }
                }                
            }
        }
    }
    else
    {
        # Socket failed
        if (is_object($Src))
        {
            cacheErrorHandler($Src) ;
        }
        elseif (is_object($Compare))
        {
            cacheErrorHandler($Compare) ;
        }
    }
}
class Stat {
    public $skill ;
    public $rank ;
    public $level ;
    public $exp ;
    public $f2p ;                            
    public $next ;
    public $penguin ;
    public $zeal ;
    public $tog ;
    function calcTog($Lowest)
    {
        $this->tog = ($this->level >= 30) ? (int)($this->next / 60) : (int)(ceil($this->next / min(array((100 + floor($Lowest / 27)) / 10)))) ;
    }   
    function toString($Mode = STATS_ALL) {
        if (empty($this->exp))
        {
            return sprintf("%s %d %d", $this->skill, $this->rank, $this->level) ;
        }            
        switch ($Mode)
        {
            case STATS_RANK:
                return sprintf("%s %d", $this->skill, $this->rank) ;
                break ;
            case STATS_LEVEL:
                return sprintf("%s %d", $this->skill, $this->level) ;
                break ;
            case STATS_EXP:
                return sprintf("%s %d", $this->skill, $this->exp) ;
                break ;
            default:
                return sprintf("%s %d %d %d %d %d %d", $this->skill, $this->rank, $this->level, $this->exp, $this->next, $this->penguin, $this->tog) ;
            break;
        }
    }    
}
function generateStats ($Src)
{   
    global $Skills, $F2p ; 
    $Src = explode(chr(10), $Src) ;
    $Count = count($Src) - 1 ;
    
    $Stats = new stdClass ;
    $Stats->Unranked = 0 ;
    $Stats->UnrankedSkill = 0 ;
    $Stats->TotalLevels = 0 ;
    $Stats->TotalExp = 0 ;

    $Stats->Stats = array() ;
    $Stats->Levels = array() ;
    
    $Stats->data = array(
        'highest' => null ,
        'lowest' => null
    ) ;
    
    $Stats->totals = array(
        'cmbExp' => 0,
        'skillExp' => 0,
        'f2pExp' => 0,
        'p2pExp' => 0
    ) ;
    
    $Overall = explode(',', $Src[0]) ;
    $Stats->Overall = array (
        'rank' => $Overall[0],
        'level' => $Overall[1],
        'exp' => $Overall[2]
    ) ;
    
    for ($a = 1; $a < $Count; $a++)
    {
        $Skill = explode(',', $Src[$a]) ;
        if ($Skill[0] == '-1')
        {
            if ($a > 0 && $a < 26)
            {
                $Stats->Unranked++ ;
                $Stats->UnrankedSkill = $a ;
            }
            continue ;
        }
        else
        {
            if ($a > 0 && $a < 26)
            {
                $Stats->TotalLevels += (int)($Skill[1]) ;
                $Stats->TotalExp += (int)($Skill[2]) ;
            }  
                              
            $Stats->Levels[$Skills[0][$a]] = (int)($Skill[1]) ;
                   
            $Object = new Stat ;
            $Object->skill = $Skills[0][$a] ;
            $Object->rank = (int)$Skill[0] ;
            $Object->level = (int)((($Skill[1] == '99' || $Skill[1] == '120') && !empty($Skill[2])) ? undoexp($Skill[2]): $Skill[1]) ;
            $Object->exp = (int)((empty($Skill[2])) ? null: $Skill[2]) ;
            $Object->f2p = isset($F2p[$Object->skill]);                            
           
            if ($a > 0 && $a < 26)
            {
                if ($Object->level != 126)
                {
                    $Object->next = (int)(statsxp($Object->level + 1) - $Object->exp) ;
                    $Object->penguin = (int)penguin($Object->next, $Object->level) ;
                    $Object->zeal = (int)zeal($a, $Object->level) ;
                    $Object->pc = (int)(pcontrol($a, $Object->level)) ;
                }
                
                if ($Stats->data['highest'] == null) { $Stats->data['highest'] = $Object->exp ; }
                elseif ($Object->exp > $Stats->data['highest']) { $Stats->data['highest'] = $Object->exp ; }
                if ($Stats->data['lowest'] == null) { $Stats->data['lowest'] = $Object->exp ; }
                elseif ($Object->exp < $Stats->data['lowest']) { $Stats->data['lowest'] = $Object->exp ; }          

                if (in_array($a, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 12, 13, 14, 15, 21))) { $Stats->totals['f2pExp'] += $Object->exp ; }  
                if (in_array($a, array(11, 16, 17, 18, 19, 20, 22, 23, 24, 25))) { $Stats->totals['p2pExp'] += $Object->exp ; }  
                if (in_array($a, array(1, 2, 3, 4, 5, 6, 7, 24))) { $Stats->totals['cmbExp'] += $Object->exp ; }  
                if (in_array($a, array(8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25))) { $Stats->totals['skillExp'] += $Object->exp ; }                                
            }  
            else
            {
                $Object->next = 0 ;
                $Object->penguin = 0 ;
                $Object->zeal = 0 ;
            }                          
            $Stats->Stats[$Object->skill] = $Object ;
        }
    }
    return $Stats ;
}
function genString($Skills, $List, $Current, $Up = 1)
{
    $Array = array() ;
    foreach ($List as $Skill)
    {
        if ($Skill == $Current) { $Array[$Skill] = (int)((!empty($Skills[$Skill])) ? $Skills[$Skill] : 1) + (int)$Up; }
        else { $Array[$Skill] = (int)((!empty($Skills[$Skill])) ? $Skills[$Skill] : 1) ; }
    }
    return $Array ;
}
function nextcmb ($Skills, $Combat, $Return = false) 
{
    if (floor($Combat) == 138) { return 'None' ; }   
    $List = array('Attack', 'Defence', 'Strength', 'Constitution', 'Prayer', 'Magic', 'Ranged', 'Summoning') ;
    $LevelUp = array() ;
    $N = 0 ;
    $Up = 0 ;
    $Newcmb = $Combat ;

    while ($N < count($List))
    {
        $Skill = $List[$N] ;
        if ($Skill == 'Constitution')
        {
            /* Constitution starts at 10, not 1 */
            $Level = (!empty($Skills[$Skill])) ? $Skills[$Skill] : 10 ;
        }    
        else
        {
            /* Account for the constitution starting higher */
            $Level = (!empty($Skills[$Skill])) ? $Skills[$Skill] : 1 ;
        }    
        if (empty($Skills[$Skill]))
        {
            /* Fix incase combat is unreanked */
            $Skills[$Skill] = $Level ;
        }
        if ($Level >= 99) { $Up = 0 ; $N++ ; continue ; }
        $Up++ ;
        $Array = genString($Skills, $List, $Skill, $Up) ;
        $Newcmb = cmbformula($Array, 2) ;
        if (floor($Newcmb) > floor($Combat)) { $LevelUp[$Skill] = $Up ; $N++ ; $Up = 0 ; continue ; }
        if (($Level + $Up) == 99) { $N++ ; $Newcmb = $Combat ; $Up = 0 ; }
    }

    if ($Return)
    {
        foreach ($LevelUp as $Skill => $Levels)
        { 
            $Skills[$Skill] = $Skills[$Skill] + (int)$Levels ;
        }
        $Skills['Combat'] = $Newcmb ;
        return $Skills ;
    }

    $String = array() ;
    foreach ($LevelUp as $Skill => $Levels)
    { $String[] = $Skill . ' ' . $Levels ; }

    return implode(' | ', $String) ;
}
function penguin($Next, $Level)
{
    $Level = ($Level > 99) ? 99 : $Level ;
    return ceil($Next / ($Level * 25)) ;
}
function zeal($Skill, $Level)
{
    $Level = ($Level > 99) ? 99 : $Level ;
    #Soul Wars
    #Attack Def Str HP = floor((Level*Level)*0.875) 
    if (in_array($Skill, array(1, 2, 3, 4)))
    {
        return floor(($Level * $Level) * 0.875) ;
    }
    #Mage Range = floor((Level*Level)*0.80)
    elseif ($Skill == 5 || $Skill == 7)
    {
        return floor(($Level * $Level) * 0.80) ;
    }
    #Prayer = floor((Level*Level)*0.45)
    elseif ($Skill == 6)
    {
        return floor(($Level * $Level) * 0.45) ;
    }
    #Slayer = CEILING((Slayer+9)*(Slayer-10)/(7.5*45),1)*45
    elseif ($Skill == 19)
    {
        return (int)(ceil(($Level + 9)*($Level - 10)/(7.5 * 45)) * 45) ;
    }
    else { return 0 ; }
}
function pcontrol($Skill, $Level)
{
    $Level = ($Level > 99) ? 99 : $Level ;
    #Soul Wars
    #Attack Def Str HP = floor((Level*Level)*0.875) 
    if (in_array($Skill, array(1, 2, 3, 4)))
    {
        return floor(($Level * $Level) / 600 * 35) ;
    }
    #Mage Range = floor((Level*Level)*0.80)
    elseif ($Skill == 5 || $Skill == 7)
    {
        return floor(($Level * $Level) / 600 * 32) ;
    }
    #Prayer = floor((Level*Level)*0.45)
    elseif ($Skill == 6)
    {
        return floor(($Level * $Level) / 600 * 18) ;
    }
    else { return 0 ; }
}
function isOdd($Num) 
{
    #if (strstr((String)($Num / 2), '.')) { return true ; }
    #return false ;
    return !(($Num % 2) == 0) ;
}
function cmbformula($Stats, $P2p = 0)
{    
    global $CombatLevel ;
    
    $Attack   = (!empty($Stats['Attack']) && $Stats['Attack'] > 0) ? $Stats['Attack'] : 1 ;
    $Defence  = (!empty($Stats['Defence']) && $Stats['Defence'] > 0) ? $Stats['Defence'] : 1 ;
    $Strength = (!empty($Stats['Strength']) && $Stats['Strength'] > 0) ? $Stats['Strength'] : 1 ;
    $HP       = (!empty($Stats['Constitution']) && $Stats['Constitution'] > 0) ? $Stats['Constitution'] : 10 ;
    $Range    = (!empty($Stats['Ranged']) && $Stats['Ranged'] > 0) ? $Stats['Ranged'] : 1 ;
    $Pray     = (!empty($Stats['Prayer']) && $Stats['Prayer'] > 0) ? $Stats['Prayer'] : 1 ;
    $Mage     = (!empty($Stats['Magic']) && $Stats['Magic'] > 0) ? $Stats['Magic'] : 1 ;
    $Summon   = (!empty($Stats['Summoning']) && $Stats['Summoning'] > 0 && $P2p != 1) ? $Stats['Summoning'] : 1 ;
    
    $A = $Defence * 100 ;
    $B = $HP * 100 ;
    
    if (isOdd($Pray)) 
    { $C = ($Pray - 1) * 50 ; }
    else
    { $C = $Pray * 50 ; }
    
    if (isOdd($Summon)) 
    { $D = ($Summon - 1) * 50 ; }
    else
    { $D = $Summon * 50 ; }
    
    $Base = ($A + $B + $C + $D) / 400 ;
    $E = $Attack * 130 ;
    $G = $Strength * 130 ;
    
    if (isOdd($Range)) 
    { $H = ($Range * 195) - 65 ; }
    else
    { $H = $Range * 195 ; }
    
    if (isOdd($Mage)) 
    { $I = ($Mage * 195) - 65 ; }
    else
    { $I = $Mage * 195 ; }
    
    $Melee = ($E + $G) / 400 ;
    $Range = $H / 400 ;
    $Mage = $I / 400 ;
    
    $Combat = $Melee ;
    $CombatType = 'Melee' ;
    if ($Range > $Combat) {
        $Combat = $Range;
        $CombatType = 'Range' ;
    }
    if ($Mage > $Combat) {
        $Combat = $Mage;
        $CombatType = 'Mage' ;
    }
    $Combat = $Combat + $Base ; 
    if ($Combat < 3)
    {
        $Combat = 3 ;
    }
    if ($P2p == 2) { return $Combat; }
    if ($P2p == 0) { $CombatLevel = $Combat ; }
    return $Combat . ' ' . $CombatType ;  
}

?>