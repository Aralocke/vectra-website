<br />
<table width="95%" border="0" align="center" cellpadding="4" cellspacing="10" bgcolor="0A1646">
  <tr>
    <td><h1 align="center" class="lolclassss">N<span class="gray">ews</span></h1></td>
  </tr>
</table>
<br />
<br />
<?php
//
// Scripted by Thomas
//

$MyphpBBVersionIs = 2; // Fill inn either 2 or 3 in the varible $MyphpBBVersionIs depending on your phpBB version
$NewsForum = 1; // The forum (after ?f=) the news is beeing taken from
$NumberOfNews = 5; // Number of news posted
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
	
		echo "<h1 class='style3'>". $row[topic_title] . "</h1>";
		
		echo "<br />";
		echo "<br />";

		echo $row['post_text'];

		echo " <div class='box'>
		<table width='100%' border='0'>
		<tr>
		<td>";
		  
		echo "<b>Poster: </b><a href='/forum/profile.php?mode=viewprofile&u=" . $row[topic_poster] . "' target='_blank' >" . $row[username] . "</a>";
		 
		echo "
		</td>
		<td>";
		
		echo "<b>Published: </b>";
		echo date( 'd M Y H:i', $row['topic_time'] );
		
		echo "
		</td>
		<td>";
		
		echo "<b>Comments: </b> $row[topic_replies]";
		
		echo "
		</td>
		<td>";
		
		echo "<a href='$MySiteURLforum$ForumPath/viewtopic.php?t=" . $row[topic_id] . "' target='_blank' >Read More »</a>";
		
		echo "
		</td>
		</tr>
		</table> </div><br />";
		
		echo "<br />";
		echo "<br />";
		
		//Slutt på Infobox
	}
	
	echo "<a href='http://vectra-bot.org/forum/viewforum.php?f=$NewsForum'>&laquo; Old News</a>";
	mysql_free_result($result);

?>