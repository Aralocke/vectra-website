<?php
//
// Scripted by Even
// Credits to Thomas
//

$MyphpBBVersionIs = 2; // Fill inn either 2 or 3 in the varible $MyphpBBVersionIs depending on your phpBB version
$NewsForum = 1; // The forum (after ?f=) the news is beeing taken from
$NumberOfNews = 10; // Number of news posted
$MySiteURL = "http://www.vectra-bot.org"; // Your URL
$ForumPath = "/forum/"; // The path to the forum folder

// Connection info
$host = "mysql1031.servage.net";
$user = "Vectra-forum";
$password = "evenerbest123";
$Database = "Vectra-forum";


	$connection = mysql_connect($host,$user,$password);
	mysql_select_db($Database, $connection);
	
	$sql= "SELECT t1.topic_poster, t1.topic_replies, t1.topic_title, t1.topic_time, t1.forum_id, t1.topic_id, t2.username, t3.post_text, t3.post_id, t3.post_subject FROM phpbb_topics t1, phpbb_users t2, phpbb_posts_text t3 WHERE t1.topic_poster = t2.user_id AND t1.forum_id = $NewsForum AND t1.topic_title = t3.post_subject ORDER BY t1.topic_time DESC LIMIT $NumberOfNews";


	// [topic_poster] = Brukeren som har skrevet tråden
	// [username] = Brukeren som har skrevet tråden
	// [topic_replies] = Antall svar i tråden
	// [topic_title] = Titelen på tråden
	// [post_subject] = Titelen på tråden
	// [topic_time] = dato/tid tråden ble skrevet
	// [forum_id] = Hvilke ID forumet tråden ligger i
	// [topic_id] = Hvilke ID tråden har
	// [post_text] = Selve teksten i 1. post i tråden
	// [post_id] = Hvilke ID selve posten har

	// Stater looping og echoing av innhold fra databasen
	$result=mysql_query($sql);
	while ($row=mysql_fetch_assoc($result))
	
	{	

		echo $row[topic_title] .",". $row[username] .",http://vectra-bot.org/forum/viewtopic.php?t=". $row[topic_id] .",". date( 'd M Y H:i', $row['topic_time'] );
		echo "
";
		//Slutt på Infobox
	}
	mysql_free_result($result);

?>