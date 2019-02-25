<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

$Amount = 1 ;
if (!empty($_GET['amount']) && (is_numeric(stringToNum($_GET['amount'])) && stringToNum($_GET['amount']) > 0))
{
    $Amount = stringToNum($_GET['amount']) ;
}
$Spell = null ;
if (!empty($_GET['spell']))
{
    $Spell = trim(urldecode($_GET['spell'])) ;
}
if (empty($Spell))
{
    echo 'ERROR: Missing parameter &spell' . chr(10) ;
}
else 
{
    $Dbc->connect() ;
    
    $Query = "
        SELECT * FROM `Parsers`.`rs_spells` 
        WHERE Name 
        LIKE '%%".$Dbc->sql_escape(str_replace(' ', '%', $Spell))."%%'
        LIMIT 1
    ";
    
    $Result = $Dbc->sql_query($Query) ;
    if ($Dbc->sql_num_rows($Result) > 0)
    {
        $Obj = $Dbc->sql_fetch($Result) ;
        echo 'SPELL: ' . $Obj->Name . chr(10) ;
        echo 'LEVEL: ' . $Obj->Level . chr(10) ;
        echo 'EXP: ' . $Obj->Exp . chr(10) ;
        echo 'DAMAGE: ' . $Obj->MaxDamage . chr(10) ;
        echo 'SPECIAL: ' . $Obj->Special . chr(10) ;
        
        
        $Runes = json_decode($Obj->Runes);
		$Total = array(0, 0, 0);			
		echo 'RUNES: ';
		foreach ($Runes as $Rune) {
			$Name = getItemById($Rune[0]);
			$Total[0] += ($Rune[1] * $Name->price * $Amount);
			printf("%dx_%s ", $Rune[1] * $Amount, str_replace(' ', '_', $Name->name));
		}
		echo chr(10) ;
		printf("COST: %s\n", $Total[0]);
        
        $Dbc->sql_freeresult($Result) ;
    }
    else
    {
        echo 'ERROR: No results found for "' . $Spell . '"' . chr(10) ;
    }
}
?>