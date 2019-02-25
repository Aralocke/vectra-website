<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) 
{
    exit;
} 

if (empty($_GET['q'])) {
	print "ERROR: Missing argument `q`\n";
} 
else {
	$q 			= urlencode($_GET['q']);
	$bad 		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
	$cacheFile	= "Urban." . str_replace($bad, '_', $_GET['q']);
	$src 		= httpCacheSocket(
        "GET",
        "http://urbandictionary.com", 
        "/define.php?term={$q}", 
        $cacheFile, 120, 
        null, 0
    );
	if (!is_object($src)) {
		if (strrpos($src, "<div id='not_defined_yet'>")) {
			print "ERROR: " . urldecode($q) . " returned no results\n";
		} else {
			preg_match_all('@<div class="definition">(.+?)</div><div class="example">(.+?)</div>\s+@isS', $src, $match);
			print "RESULTS: " . count($match[1]) . "\n";
			for ($i = 0; $i < count($match[1]); $i++) {
				$temp = array(); $n = $i+1;
				preg_match('@(.+?)(?:<br/>|$)@i', $match[1][$i], $temp[]);
				preg_match('@(.+?)(?:<br/>|$)@i', $match[2][$i], $temp[]);
				print "DEFINITION#{$n}: " . strip_tags($temp[0][1]) . "\n";
				print "EXAMPLE#{$n}: " . strip_tags($temp[1][1]) . "\n";
			}
		}
	} else {
		cacheErrorHandler($src);
	}
}
?>