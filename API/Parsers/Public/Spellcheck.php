<?php
if (!defined("IN_PARSERS") OR !IN_PARSERS) {
	exit;
} 
if (empty($_GET['q'])) {
	print "ERROR: Missing argument `q`\n";
} 
else {
	$_Link 		= array("http://dictionary.reference.com", "/browse/" . urlencode($_GET['q']));
	$bad 		= array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
	$cacheFile 	= "SpCheck." . str_replace($bad, '_', $_GET['q']);
	$src = httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120) ;
	if (!is_object($src)) 
    {
		print "WORD: {$_GET['q']}\n";
		if (strrpos($src, "No results found for")) 
        {
			preg_match_all('@<div id="spellSuggestWrapper"><li class="result_list"><a href="[^"]+">([^<]+)</a></li></div>@i', $src, $suggestion);
			print "CHECK: Incorrect\n";
			print "SUGGESTIONS: " . implode(", ", $suggestion[1]) . "\n";
		} 
        else 
        {
			print "CHECK: Correct\n";
		}
	} 
    else 
    {				
        cacheErrorHandler($src);
	}
}	
?>