<?php
$rsname1 = $_GET['user1'];
$skill = $_GET['skill'];
$rsname2 = $_GET['user2'];

$url1 = "http://hiscore.runescape.com/overall.ws?table=";
$url1 .= $skill;
$url1 .= "&user=";
$url1 .= $rsname1;

$url2 = "http://hiscore.runescape.com/overall.ws?table=";
$url2 .= $skill;
$url2 .= "&user=";
$url2 .= $rsname2;

$data1 = file_get_contents($url1);
$data2 = file_get_contents($url2);

preg_match_all("/<span style=\"color:#AA0022;\">([^<]+?)<\/span>/", $data1, $matches1);
preg_match("/<font color=\"#AA0022\">([^<]+?)<\/font>/", $data1, $matches1a);
preg_match("/\n([^<]+?)\nHiscores\n<\/caption>/", $data1, $matches1b);
preg_match_all("/<span style=\"color:#AA0022;\">([^<]+?)<\/span>/", $data2, $matches2);
preg_match("/<font color=\"#AA0022\">([^<]+?)<\/font>/", $data2, $matches2a);
preg_match("/\n([^<]+?)\nHiscores\n<\/caption>/", $data2, $matches2b);

$rank1 = $matches1[1][0];
$rank1 = str_replace("&nbsp;","",$rank1);
$name1 = $matches1[1][1];
$level1 = $matches1[1][2];
$exp1 = $matches1a[1];
$skill1 = str_replace("&nbsp;", "", $matches1b[1]);
$skill1 = str_replace(chr(10), "", $skill1);
$skill1 = str_replace(chr(13), "", $skill1);

$rank2 = $matches2[1][0];
$rank2 = str_replace("&nbsp;","",$rank2);
$name2 = $matches2[1][1];
$level2 = $matches2[1][2];
$exp2 = $matches2a[1];
$skill2 = str_replace("&nbsp;", "", $matches2b[1]);
$skill2 = str_replace(chr(10), "", $skill2);
$skill2 = str_replace(chr(13), "", $skill2);

echo "User1: $rank1|$name1|$skill1|$level1|$exp1!$rank2|$name2|$skill2|$level2|$exp2
";
?>