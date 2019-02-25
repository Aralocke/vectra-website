<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
if (!empty($_GET['item'])) {
    $Lookup = str_replace(array('%20','_'),' ',trim($_GET['item'])) ;
}
if (!empty($_GET['skill'])) {
    $Skill = ucwords(strtolower(trim($_GET['skill']))) ;
    $Cost_Skills = array(16,23,8,13,12,10,6,14) ;
    if (isset($Skills[2][$Skill])) { 
        $Skill = $Skills[2][$Skill] ;
    }
    elseif (isset($Skills[1][$Skill])) { 
        $Skill = $Skill ;
    }
}
if (!isset($Lookup)) { 
    echo 'ERROR: Missing arguement &item ' . chr(10) ;
}
elseif (!isset($Skill)) { 
    echo 'ERROR: Missing arguement &skill ' . chr(10) ;
}
elseif (!is_numeric($Skill) || !in_array($Skill,$Cost_Skills) || $Skill < 0 || $Skill > count($Skills[1])) {
    echo 'ERROR: The arguement &skill must be a valid skill' . chr(10) ;
}
else {
    $Amount = (int) 1 ;
    if (!empty($_GET['amount']) && is_numeric($_GET['amount'])) {
        $Amount = $_GET['amount'] ;
        if ($Amount < 0) {
            $Amount = 1 ;
        }
    }
    
    $Dbc->connect() ;
    $Query = "SELECT * FROM `Parsers`.`SkillParameters` 
    WHERE `item` LIKE '%%".$Dbc->sql_escape(str_replace(' ', '%', $Lookup))."%%' 
    AND `skill` = '".$Skill."' LIMIT 1" ;
        
        $Result = $Dbc->sql_query($Query) ;
        if ($Dbc->sql_num_rows($Result) > 0) {
            $Obj = $Dbc->sql_fetch($Result) ;
            echo 'ITEM: ' . $Obj->item . chr(10) ;
            echo 'SKILL: ' . $Skills[1][$Obj->skill] . chr(10) ;
            echo 'Level: ' . $Obj->level . chr(10) ;
            echo 'EXP: ' . $Obj->exp . chr(10) ;
            
            $Items = json_decode($Obj->components) ;
            (int) $Total = 0;
            echo 'ITEMS: ' ;
            foreach ($Items as $Item) {
                $Data = getItemById($Item->Id);
                $Total += ($Item->Num * $Data->price * $Amount);
                printf("%dx_%s ", $Item->Num * $Amount, str_replace(' ', '_', $Data->name));
            }
            echo chr(10) . 'COST: ' . $Total . chr(10);
            $Ge = getItemByID($Obj->geid);
            if(!is_object($Ge)) {
                $Ge = new stdClass ;
                $Ge->price = 0;  
            }
           $Difference = $Ge->price - $Total ;
           $Gp_Exp = round($Difference/ $Obj->exp,2) ;
           echo 'ITEMCOST: ' . (($Ge->price == 0) ? $Total : $Ge->price) . chr(10) ;
           echo 'DIFFERENCE: ' . $Difference . chr(10) ;
           echo 'GP/EXP: ' . (($Gp_Exp >= 0) ? '+' . $Gp_Exp : $Gp_Exp) . chr(10) ;
           
           $Dbc->sql_freeresult($Result) ;
        }
        else { 
            echo 'ERROR: No results found for "' . $Lookup . '"' . chr(10) ;
        }
}
?>