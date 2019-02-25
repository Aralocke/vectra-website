<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) 
{
    exit;
}

$Slot = array(
    1 => 'Head',
    2 => 'Neck',
    3 => 'Chest',
    4 => 'Weapon',
    5 => 'Shield',
    6 => 'Arrows',
    7 => 'Legs',
    8 => 'Feet',
    9 => 'Hands',
    10 => 'Ring',
    11 => 'Cape'
);

$Source = array (
    1 => 'Drop',
    2 => 'Vendor',
    3 => 'Quest',
    4 => 'Cooking',
    5 => 'Crafting',
    6 => 'Construction',
    7 => 'Herblore',
    8 => 'Mining',
    9 => 'Smithing',
    10 => 'Fletching',
    11 => 'Fishing',
    12 => 'Farming',
    15 => 'Thieving',
    13 => 'Woodcutting',
    14 => 'Runecrafting'
);

$Style = array(
    1 => 'Melee',
    2 => 'Ranged',
    3 => 'Magic'
);

$iStats = array (
    'Attack' => array (
        1 => 'Stab',
        3 => 'Slash',
        5 => 'Crush',
        7 => 'Magic',
        9 => 'Range'
    ),
    'Defence' => array (
        2 => 'Stab',
        4 => 'Slash',
        6 => 'Crush',
        8 => 'Magic',
        10 => 'Range',
        11 => 'Summon'
    ),
    'Other' => array (
        12 => 'Strength',
        13 => 'Ranged Strength',
        14 => 'Prayer'
    )
) ;

$Rarity = array(
    0 => 'Common',
    1 => 'Common',
    2 => 'Rare',
    3 => 'Legendary',
    4 => 'Unique'
) ;

define('NATURE_RUNE', 561);

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
        $Query = "SELECT * FROM itemz WHERE id = '".(int)$Search."'" ;
        $Result = $Dbc->sql_query($Query);
        $Result = ($Dbc->sql_num_rows($Result) > 0) ? $Result : false ;
    }
    else
    {
        $Result = sqlSearch('Parsers', 'itemz', 'name', $Dbc->sql_escape($Search), 5) ;
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
                echo 'ITEM: '.str_replace(' ', '_', $Obj->name) . ' #' . $Obj->id . chr(10);
                $Count++ ;
            }
        }
        else
        {
            $Obj = $Dbc->sql_fetch($Result);
            echo 'NAME: ' . str_replace(' ', '_', $Obj->name) . chr(10);
            $Link = 'http://www.zybez.net/item.aspx?id=' . $Obj->id ;
            echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10) ;
            if ($Obj->ge_id != 0)
            {
                $GeData = getItemById($Obj->ge_id) ;
                echo 'GE: ' . $GeData->price . chr(10) ;
                $Nature = getItemById(NATURE_RUNE) ;
                echo 'NATURE: ' . $Nature->price . chr(10) ;
            }
            echo 'SOURCE: ' . ((!empty($Obj->source))?$Source[$Obj->source]:'Unknown') . chr(10) ;
            echo 'MEMBERS: ' . $Obj->members . chr(10) ;
            echo 'RARITY: ' . ((!empty($Rarity[$Obj->rarity])) ? $Rarity[$Obj->rarity] : $Obj->rarity) . chr(10) ;
            echo 'SPEED: ' . $Obj->speed . chr(10) ;
            echo 'SLOT: ' . ((!empty($Slot[$Obj->slot]))?$Slot[$Obj->slot]:'Inventory') . chr(10) ;
            echo 'QUEST: ' . (($Obj->quest == 1)?'Yes':'No') . chr(10) ;
            echo 'TRADE: ' . (($Obj->trade == 1)?'Yes':'No') . chr(10) ;
            echo 'STACK: ' . (($Obj->stack == 1)?'Yes':'No') . chr(10) ;
            echo 'EQUIP: ' . (($Obj->equip == 1)?'Yes':'No') . chr(10) ;
            echo 'TWOHANDED: ' . (($Obj->twohanded == 1)?'Yes':'No') . chr(10) ;
            echo 'WEIGHT: ' . $Obj->weight . chr(10) ;
            echo 'EXAMINE: ' . $Obj->examine . chr(10) ;
            echo 'HIGH: ' . $Obj->highalch . chr(10) ;
            echo 'LOW: ' . $Obj->lowalch . chr(10) ;
            $Stats = json_decode($Obj->stats) ;
            if ($Stats != false)
            {
                $iStat = array(
                    'Attack' => array(),
                    'Defence' => array(),
                    'Other' => array()
                ) ;
                foreach ($Stats as $Stat)
                {
                    if (isset($iStats['Attack'][$Stat->id])) 
                    {
                        $iStat['Attack'][] = $iStats['Attack'][$Stat->id] . ' ' . (($Stat->val > 0)?'+':'').$Stat->val ;
                    }
                    elseif (isset($iStats['Defence'][$Stat->id])) 
                    {
                        $iStat['Defence'][] = $iStats['Defence'][$Stat->id] . ' ' . (($Stat->val > 0)?'+':'').$Stat->val ;
                    }
                    elseif (isset($iStats['Other'][$Stat->id])) 
                    {
                        $iStat['Other'][] = $iStats['Other'][$Stat->id] . ' ' . (($Stat->val > 0)?'+':'').$Stat->val ;
                    }
                }
                $iStat['Attack'] = (!empty($iStat['Attack'])) ? 'Attack: ' . implode(' | ', $iStat['Attack']) : null ;
                $iStat['Defence'] = (!empty($iStat['Defence'])) ? 'Defence: ' . implode(' | ', $iStat['Defence']) : null ;
                $iStat['Other'] = (!empty($iStat['Other'])) ? 'Other: ' . implode(' | ', $iStat['Other']) : null ;
                echo 'STATS: ' . implode(' ', $iStat) . chr(10) ;
            }
        }
        $Dbc->sql_freeresult($Result) ;
    }
}
?>