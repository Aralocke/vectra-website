<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) 
{
  exit;
} 
$time = (!isset($_GET['time'])) ? 'day' : $_GET['time'];
$skill = (!isset($_GET['skill'])) ? 0 : $_GET['skill'];

$cacheFile = 'TopTrack.' . $skill;
$src = httpCacheSocket(
"GET",
'http://runetracker.org',
'/topgains-' . $skill,
$cacheFile,
120
);
if (!is_object($src)) {
  preg_match_all('#<span class="fl"><span class="gw">([1-9]|10)\.</span>&nbsp;<a href="/track-.*">(.+)</a>&nbsp;&nbsp;</span><span class="na">(.+)</span></div>#i',$src,$toptrack);
  switch ($time) {
    case 'd':
    case 'day':
    $x = 0;$y = 10;
    break;

    case 'w':
    case 'wk':
    case 'week':
    $x = 10;$y = 20;
    break;

    case 'm':
    case 'mn':
    case 'mon':
    case 'mth':
    case 'month':
    $x = 20;$y = 30;
    break;

  }
  echo 'SKILL: ' . $Skills[1][$skill] . chr(10) ;
  $Link = 'http://runetracker.org/topgains-' . $skill ;
  echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10) ;
  for (;$x<$y;$x++) {
    print 'TOPTRACK:' . ' ' . str_replace(" ","_",$toptrack[2][$x]) . ' ' . $toptrack[3][$x] . chr(10);
  }
} else {
cacheErrorHandler($src);
}
?>
