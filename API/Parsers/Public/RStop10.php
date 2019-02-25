<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) 
{
	exit;
} 


$skill 		= (!empty($_GET['skill']) && is_numeric($_GET['skill'])) ? $_GET['skill'] : 0 ;
$cacheFile	= "RSTop10" . $skill;
$src 		= httpCacheSocket(
    'GET',
    'http://services.runescape.com',
    '/m=hiscore/overall.ws?category_type=0?table='.$skill,
    $cacheFile, 120
);

if (!is_object($src)) 
{
	preg_match_all('@<tr( class=")row row\d+">([\r\n\s]+)<td\\1rankCol">\d+</td>\\2<td\\1alL"><a href="[^"]+">([^<]+)</a></td>\\2<td\\1alL">([^<]+)</td>\\2<td\\1alL">([^<]+)</td>\\2</tr>@i', $src, $top10);
	$Link = 'http://services.runescape.com/m=hiscore/overall.ws?category_type=0?table=' . $skill ;
    echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10) ;
    for ($i = 0; $i < 10; $i++) 
    {
		$name = preg_replace("@(\W)@", '_', $top10[3][$i]);
		$info = 'TOP10: ' .$name. ' ' .$top10[4][$i]. ' ' .$top10[5][$i]. '' .chr(10);
		echo $info;
	}
} 
else 
{
	cacheErrorHandler($src);
}

?>