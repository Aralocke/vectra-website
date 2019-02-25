<?php
	error_reporting(0);
	header('Content-type: text/xml');
	header('Pragma: public');        
	header('Cache-control: private');
	header('Expires: -1');
	
	$MyphpBBVersionIs = 3;  
	$NewsForum        = 1; 
	$NumberOfNews     = 10; 
	$MySiteURL        = "http://www.vectra-bot.net"; 
	$ForumPath        = "/forum/"; 	

	$Host     = "";
	$User     = "Vectra";
	$Password = "";
	$Database = "Vectra";

	$Con = mysqli_connect($Host, $User, $Password, $Database, 27011) or die("Error connecting to database\n");
	
	$Query = "
		SELECT * 
		FROM phpbb3_topics JOIN phpbb3_posts ON phpbb3_topics.topic_first_post_id = phpbb3_posts.post_id
		WHERE phpbb3_topics.forum_id = '{$NewsForum}'
		ORDER BY phpbb3_topics.topic_id DESC
	";
	
	$Sql = mysqli_query($Con, $Query);
	$Rows = mysqli_num_rows($Sql);
	$Count = 0;
	echo('<?xml version="1.0" encoding="utf-8"?>');
	echo "<vectra>\n";
	while ($Rows > 0 && ($Row = mysqli_fetch_array($Sql, 1)) && $Count < 10) {
		echo "<news>\n";
		printf("<link>http://vectra-bot.net/forum/viewtopic.php?f=1&amp;t=%s</link>\n", trim($Row["topic_id"]));
		printf("<poster>%s</poster>\n", $Row["topic_first_poster_name"]);
		printf("<header>%s</header>\n", $Row["topic_title"]);
		printf("<content><![CDATA[%s]]></content>\n", parseText($Row["post_text"], $Row['bbcode_uid']));
		printf("<comments>-</comments>\n");
		printf("<Date>%s</Date>\n", date('d M Y', $Row['post_time']));
		echo "</news>\n";
		$Count++;
	} 
	echo "</vectra>\n";
	mysqli_close($Con);
	
function parseText($Text, $BBcode) {
	# Parse out all bbcode uids
	$Text = str_replace(":{$BBcode}", '', $Text);
	return preg_replace("|[[\/\!]*?[^\[\]]*?]|si", "", $Text);
}
?>