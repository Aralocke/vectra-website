<?php
$type = $_GET["type"] ;
$skills = array( "OVERALL", "ATTACK", "DEFENCE", "STRENGTH", "HITPOINTS",
    "RANGED", "PRAYER", "MAGIC", "COOKING", "WOODCUTTING", "FLETCHING", "FISHING",
    "FIREMAKING", "CRAFTING", "SMITHING", "MINING", "HERBLORE", "AGILITY",
    "THIEVING", "SLAYER", "FARMING", "RUNECRAFT", "HUNTER", "CONSTRUCTION",
    "SUMMONING" ) ;
if ( isset($type) && $type == "top10" )
{
    $skill = $_GET["skill"] ;
    if ( !$skill || $skill < 0 || $skill > 24 )
    {
        echo "<pre>\nSTART\nPHP:Supply a skill number. &skill=\n" ;
        for ( $x = 0; $x <= 24; $x++ )
        {
            echo "Skill:" . $skills[$x] . " = " . $x . " \n" ;
        } //for ($x = 0; $x <= 24; $x++)
        echo "END" ;
    } //if ($skill < 0 || $skill > 25)
    else
    {
        $file = curl_sock( "http://hiscore.runescape.com/overall.ws?table=" . $skill .
            "&category_type=0" ) ;
        if ( $file )
        {
            $file = explode( "\n", removeEmptyLines($file) ) ;
            echo "<pre>\nSTART\n" ;
            $i = 1 ;
            for ( $x = 302; $x < count($file); $x++ )
            {
                if ( $i > 10 )
                {
                    break ;
                }
                if ( strstr($file[$x], "<td class=\"rankCol\">" . $i . "</td>") )
                {
                    $name = str_replace( " ", "_", strip_tags($file[$x + 1]) ) ;
                    $level = strip_tags( $file[$x + 2] ) ;
                    $exp = strip_tags( $file[$x + 3] ) ;
                    echo "TOP10: " . $i . " " . $exp . " " . $level . " " . $name . "\n" ;
                    $i++ ;
                }
            }
            echo "END" ;
        } else
        {

        }
    } //else
} elseif ( isset($type) && $type == "stats" )
{
    $search = $_GET["user"] ;
    if ( isset($search) )
    {
        $z = strlen( $search ) ;
        if ( $z > 12 )
        {
            die( "<pre>\nSTART \nPHP: Invalid username \nEND" ) ;
        } //if ($z > 12)
        //if ($z > 12 || $z < 3)
        else
        {
            $file = curl_sock( "http://hiscore.runescape.com/index_lite.ws?player=" . $search ) ;
            if ( strpos($file, "404 - Page not found") )
            {
                die( "<pre>\nSTART\nPHP: User " . $user . " not found in highscores. \nEND" ) ;
            } //if (strpos($file, "404 - Page not found"))
            else
            {
                $break = explode( "\n", $file ) ;
                echo "<pre>\nSTART\n" ;
                    for ( $a = 0; $a <= 24; $a++ )
                    {
                        $data = explode( ",", $break[$a] ) ;
                        if ( $data[1] != -1 )
                        {
                            echo "STAT:" . $skills[$a] . " " . $data[0] . " " . $data[1] . " " . $data[2] .
                                "\n" ;
                        }
                    }                 
                echo "END" ;
            } //else
            //else
        } //else
    } //if (isset($search))
    //if (isset($search))
    else
    {
        die( "<pre>\nSTART\nPHP: Supply username\nEND" ) ;
    } //else
    //else
} elseif ( isset($type) && $type == "ge" )
{
    echo "<pre>\nSTART\n" ;
    if ( $_GET["item"] )
    {
        $a = array( "+", "-", "_", " " ) ;
        $item = str_replace( $a, "%20", $_GET["item"] ) ;
        $itemExact = str_replace( "%20", "_", $item ) ;
        $search = ( $_GET["mode"] == "e" ) ? "\"" . $item . "\"" : $item ;
        $mode = ( $_GET["mode"] == "e" ) ? "EXACT" : "BROAD" ;
        $data = curl_sock( "http://itemdb-rs.runescape.com/results.ws?query=" . $search .
            "&price=all&members=" ) ;
        if ( strpos($data, "did not return any results") )
        {
            echo "PHP:Search returned zero results" ;
        } else
        {
            echo "MODE: " . $mode . "\n" ;
            $data = explode( "\n", $data ) ;
            $i = 0 ;
            for ( $x = 230; $x < count($data); $x++ )
            {
                if ( strstr($data[$x], "./viewitem.ws?obj=") )
                {
                    preg_match( "/viewitem\.ws\?obj=(.+)\">/", $data[$x], $match ) ;
                    $name[$i] = str_replace( " ", "_", trim(strip_tags($data[$x])) ) ;
                    $id[$i] = $match[1] ;
                    $price[$i] = str_replace( " ", "", strip_tags($data[$x + 1]) ) ;
                    $rise[$i] = str_replace( " ", "", strip_tags($data[$x + 2]) ) ;
                    if ( strstr($data[$x + 4], "Members") )
                    {
                        $mems[$i] = "1" ;
                    } else
                    {
                        $mems[$i] = "0" ;
                    }
                    $i++ ;
                    if ( $i > 4 || ($mode == "EXACT" && $i == 1) )
                    {
                        break ;
                    }
                }
            }
            echo "RESULTS: " . $i . "\n" ;
            for ( $x = 0; $x < $i; $x++ )
            {
                if ( $mode != "EXACT" || strtolower($name[$x]) == strtolower($itemExact) )
                {
                    echo "ITEM: " . $mems[$x] . " " . $name[$x] . " " . $price[$x] . " " . $rise[$x] .
                        " " . $id[$x] . "\n" ;
                }
                else {
                	echo "PHP: Search returned zero results\n" ;
                }
            }
        }
    } else
    {
        echo "PHP:Please supply a search &item also add &mode=e for an exact search\n" ;
    }
    echo "END" ;
} else
{
    echo "<pre>\nSTART\nPHP: Please supply a type: ?type=\nEND" ;
} //else
function curl_sock( $socket )
{
    $page = curl_init() ;
    curl_setopt( $page, CURLOPT_URL, $socket ) ;
    curl_setopt( $page, CURLOPT_USERAGENT,
        'Mozilla/5.0 (Windows; U;Windows NT 6.0; en-GB; rv:1.8.1.13;) Gecko/20080311 Firefox/2.0.0.13' ) ;
    curl_setopt( $page, CURLOPT_RETURNTRANSFER, 1 ) ;
    $file = curl_exec( $page ) ;
    if ( $file )
    {
        return $file ;
    } //if ($file)
    unset( $page, $file ) ;
} 

?>