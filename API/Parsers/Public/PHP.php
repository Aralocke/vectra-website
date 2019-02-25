<?php
if (!defined('IN_PARSERS') OR !IN_PARSERS) 
{
	exit;
} 
if (empty($_GET['func'])) 
{
	print "ERROR: Please provide a function to look up\n";
} 
else {
	if (preg_match("/[^a-z0-9_]/i", $_GET['func'])) {
		print "ERROR: Illegal characters present in function name\n";
	} 
    else 
    {
		$_Link 		= array("http://php.net", "/manual-lookup.php?pattern={$_GET['func']}&lang=en");
		$cacheFile 	= "PHPFunc.{$_GET['func']}";
		$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 120);
		if (!is_object($src)) 
        {
			preg_match('@<h3 class="title">Description</h3>\s*<div class="methodsynopsis dc-description">\s*(.+?)</div>@si', $src, $func);
			if (empty($func[1])) 
            {
				preg_match('@<!-- result list start -->\s*(.+?)<!-- result list end -->@si', $src, $info);
				preg_match_all('@<a href="[^"]+">(?:<b>)?([^<]+)(?:</b>)?</a><br />@i', $info[1], $results);
				print "RESULTS: " . implode(", ", $results[1]) . "\n";
			} 
            else 
            {
				preg_match('@<li class="active"><a href="([^"]+)">.+</a></li>@i', $src, $link);
				preg_match('@<p class="para rdfs-comment">\s*(.+?)</p>@is', $src, $desc);
				print "FUNCTION: " . str_replace(array("\r", "\n"), '', strip_tags($func[1])) . "\n";
				print "DESCRIPTION: " . str_replace(array(" \r", "\n"), '', strip_tags($desc[1])) . "\n";
                $Link = "http://www.php.net/manual/en/{$link[1]}" ;
				print "LINK: ".((SHORT_LINKS) ? Google::shortUrl($Link) : $Link)."\n";
			}
		} 
        else 
        {
			catchErrorHandler($src);
		}
	}
}
?>