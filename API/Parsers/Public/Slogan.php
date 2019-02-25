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
	$cacheFile	= "Slogan." . str_replace($bad, '_', $_GET['q']);
	$src 		= httpCacheSocket(
        "GET",
        "http://thesurrealist.co.uk", 
        "/slogan.cgi?word={$q}", 
        $cacheFile, 0, 
        null, 0
    );
	if (!is_object($src)) 
    {
		$slogan = parse_html($src,'<p class="mov">Paste <b>','</b>');
		print "SLOGAN: $slogan" . chr(10);
	} 
    else {
		cacheErrorHandler($src);
	}
}
?>