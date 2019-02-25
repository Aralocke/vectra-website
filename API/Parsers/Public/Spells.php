<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}


$Amount = 1 ;
if (!empty($_GET["amount"]) && stringToNum($_GET["amount"]) > 1)
{
    $Amount = stringToNum($_GET["amount"]) ;
}
if (empty($_GET['spell']))
{
    echo 'ERROR: Missing arguement &spell' . chr(10) ;
}
else
{
    $Spell = trim(urldecode($_GET['spell'])) ;
    $Dbc->connect() ;
    
    if (isUpdating())
    {
        echo 'NOTICE: Grand Exchange Database is currently updating prices may be out of date.' . chr(10) ;
    }
    
    # sqlSearch($DbName, $TbName, $Column, $Search, $OrderBy = null)
    $Result = sqlSearch('Parsers', 'rs_spells', 'name', $Spell) ;
    
    if ($Result == false)
    {
        echo 'ERROR: No spell found for the search '.$Spell.chr(10);
    }
    else
    {
        $Obj = $Dbc->sql_fetch($Result);
        echo 'NAME: '.$Obj->Name.chr(10);
        echo 'LEVEL: '.$Obj->Level.chr(10);
        echo 'EXP: '.$Obj->Exp.chr(10);
        echo 'DAMAGE: '.$Obj->MaxDamage.chr(10);
        echo 'EFFECT: '.$Obj->Special.chr(10);
        $Runes = json_decode($Obj->Runes) ;
        $Total = array(0, 0, 0) ;
        echo 'RUNES: ' ;
        foreach ($Runes as $Rune)
        {
            $Name = getItemById($Rune[0]) ;
            $Total[0] += ($Rune[1] * $Name->min * $Amount) ;
            $Total[1] += ($Rune[1] * $Name->market * $Amount) ;
            $Total[2] += ($Rune[1] * $Name->max * $Amount) ;
            printf("%dx %s ", $Rune[1], str_replace('_', ' ', $Name->name)) ;
        }
        echo chr(10);
        echo 'COST: ' . implode(',', $Total) . chr(10) ;
    }
}

?>