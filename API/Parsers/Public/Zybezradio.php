<?php
if (!defined('IN_PARSERS') OR !IN_PARSERS) {
	exit;
}
$Link = array('http://radio.zybez.net:8000','/') ;
$Src = httpCacheSocket('GET',$Link[0],$Link[1],null,0) ;
print_r($Src) ;
?>