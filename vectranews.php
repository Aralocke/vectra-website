<?php
//
// Scripted by Even
// Credits to Thomas
//

$MyphpBBVersionIs = 2; // Fill inn either 2 or 3 in the varible $MyphpBBVersionIs depending on your phpBB version
$NewsForum = 1; // The forum (after ?f=) the news is beeing taken from
$NumberOfNews = 6; // Number of news posted
$MySiteURL = "http://www.vectra-bot.net"; // Your URL
$ForumPath = "/forum/"; // The path to the forum folder

// Connection info
$host = "localhost";
$user = "Vectra";
$password = "";
$Database = "Vectra";


	$connection = mysql_connect($host,$user,$password);
	mysql_select_db($Database, $connection);
	
	$sql= "SELECT t1.topic_poster, t1.topic_replies, t1.topic_title, t1.topic_time, t1.forum_id, t1.topic_id, t2.username, t3.post_text, t3.post_id, t3.post_subject FROM phpbb_topics t1, phpbb_users t2, phpbb_posts_text t3 WHERE t1.topic_poster = t2.user_id AND t1.forum_id = $NewsForum AND t1.topic_title = t3.post_subject ORDER BY t1.topic_time DESC LIMIT $NumberOfNews";


	// [topic_poster] = Brukeren som har skrevet tr�den
	// [username] = Brukeren som har skrevet tr�den
	// [topic_replies] = Antall svar i tr�den
	// [topic_title] = Titelen p� tr�den
	// [post_subject] = Titelen p� tr�den
	// [topic_time] = dato/tid tr�den ble skrevet
	// [forum_id] = Hvilke ID forumet tr�den ligger i
	// [topic_id] = Hvilke ID tr�den har
	// [post_text] = Selve teksten i 1. post i tr�den
	// [post_id] = Hvilke ID selve posten har

	// Stater looping og echoing av innhold fra databasen
	$result=mysql_query($sql);
	while ($row=mysql_fetch_assoc($result))
	
	{	

		echo $row[topic_title] .",". $row[username] .",http://vectra-bot.net/forum/viewtopic.php?t=". $row[topic_id] .",". date( 'd M Y H:i', $row['topic_time'] );
		echo "
";
		//Slutt p� Infobox
	}
	mysql_free_result($result);

?>