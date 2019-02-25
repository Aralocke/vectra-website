<?php
	header('Content-type: text/xml');
	header('Pragma: public');        
	header('Cache-control: private');
	header('Expires: -1');

	$MyphpBBVersionIs = 2;  // Fill inn either 2 or 3 in the varible $MyphpBBVersionIs depending on your phpBB version
	$NewsForum        = 1; // The forum (after ?f=) the news is beeing taken from
	$NumberOfNews     = 10; // Number of news posted
	$MySiteURL        = "http://www.vectra-bot.net"; // Your URL
	$ForumPath        = "/forum/"; // The path to the forum folder
	
	// Connection info
	$host     = "localhost";
	$user     = "Vectra";
	$password = "";
	$Database = "Vectra";

	$con = mysql_connect($host,$user,$password);
	 ?><?php
	// Query the database and get all the records from the news table 
	mysql_select_db($Database, $con);
	$query = "SELECT t1.topic_poster, t1.topic_replies, t1.topic_title, t1.topic_time, t1.forum_id, t1.topic_id, t2.username, t3.post_text, t3.post_id, t3.post_subject FROM phpbb_topics t1, phpbb_users t2, phpbb_posts_text t3 WHERE t1.topic_poster = t2.user_id AND t1.forum_id = $NewsForum AND t1.topic_title = t3.post_subject ORDER BY t1.topic_time DESC LIMIT $NumberOfNews";
	$rsData = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($rsData);
	$totalRows = mysql_num_rows($rsData);

// Send the headers
?><?php echo('<?xml version="1.0" encoding="utf-8"?>'); ?>
	<vectra>
	 <?php if ($totalRows > 0) { // Show if recordset not empty ?>
     	<?php do { ?>
        	<news>
            	<link>http://vectra-bot.net/forum/viewtopic.php?t=<?php echo $row['topic_id']; ?></link>
                <date><?php echo date( 'd M Y', $row['topic_time'] ); ?></date>
                <poster><?php echo $row['username']; ?></poster>
                <header><?php echo $row['topic_title']; ?></header>
                <content><![CDATA[<?php echo $row['post_text']; ?>]]></content>
                <comments><?php echo $row['topic_replies']; ?></comments>
            </news>
		<?php } while ($row = mysql_fetch_assoc($rsData)); ?>
	<?php } // Show if recordset not empty ?>
	</vectra>
<?php
mysql_free_result($rsData);
?>