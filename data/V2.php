<?php
	header('Content-type: text/xml');
	header('Pragma: public');        
	header('Cache-control: private');
	header('Expires: -1');

	$MyphpBBVersionIs = 2;  // Fill inn either 2 or 3 in the varible $MyphpBBVersionIs depending on your phpBB version
	$NewsForum        = 20; // The forum (after ?f=) the news is beeing taken from
	$NumberOfNews     = 10; // Number of news posted
	$MySiteURL        = "http://www.vectra-bot.org"; // Your URL
	$ForumPath        = "/forum/"; // The path to the forum folder
	
	// Connection info
	$host     = "";
	$user     = "Vectra-forum";
	$password = "";
	$Database = "Vectra-forum";

	$con = mysql_connect($host,$user,$password);

	// Query the database and get all the records from the news table 
	mysql_select_db($Database, $con);
	// Old query (wrong).
/*	$query = "SELECT t1.topic_poster, t1.topic_replies, t1.topic_title, 
	          t1.topic_time, t1.forum_id, t1.topic_id, t2.username,
			  t3.post_text, t3.post_id, t3.post_subject
			  FROM phpbb_topics t1, phpbb_users t2, phpbb_posts_text t3
			  WHERE t1.topic_poster = t2.user_id AND t1.forum_id = $NewsForum AND 
			  t1.topic_title = t3.post_subject
			  ORDER BY t1.topic_time DESC";*/
	// New query.
	$query = "SELECT topics.topic_poster, topics.topic_replies, topics.topic_title, 
					topics.topic_time, topics.forum_id, topics.topic_id, users.username,
					posts_t.post_text, posts_t.post_id, posts_t.post_subject
			  FROM phpbb_topics AS topics, phpbb_users AS users, phpbb_posts_text AS posts_t,
					phpbb_posts AS posts
			  WHERE
					topics.topic_poster = users.user_id AND topics.forum_id = '$NewsForum' AND 
					posts.post_id = posts_t.post_id AND posts.forum_id ='$NewsForum' AND 
					topics.topic_id = posts.topic_id
			  ORDER BY topics.topic_id, topics.topic_time DESC";
	$rsData = mysql_query($query) or die(mysql_error());
	$totalRows = mysql_num_rows($rsData);
// Send the headers
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>' . "\n"; ?>
<?php
	echo '<vectra>' . "\n";
	// Show if recordset not empty 
	if ($totalRows > 0) {
		// Get news data in a fancy-pants array.
		$news = '';
		while ($row = mysql_fetch_assoc($rsData)) {
			$topic_id = $row['topic_id'];
			if (!$news[$topic_id]) {
				$news[$topic_id] = $row;
			} else {
				$news[$topic_id]['comments'][] = $row;
			}
		}

		// Format array as XML.
		foreach ($news as $n) {
			echo '	<news>' . "\n";
			echo '		<link>http://vectra-bot.org/forum/viewtopic.php?t=' . $n['topic_id'] . '</link>' . "\n";
			echo '		<date>' . date('d M Y', $n['topic_time']) . '</date>' . "\n";
			echo '		<poster>' . $n['username'] . '</poster>' . "\n";
			echo '		<header>' .  $n['topic_title'] . '</header>' . "\n";
			echo '		<content><![CDATA[' . $n['post_text'] . ']]></content>' . "\n";
			echo '		<comments>' .  $n['topic_replies'] . '</comments>' . "\n";

			if ($n['comments']) {
				$comment_counter = 1;
				foreach ($n['comments'] as $comment) {
					echo '		<comment_text_' . $comment_counter . '>' . $comment['post_text'] . '</comment_text_' . $comment_counter . '>' . "\n";
					$comment_counter++;
				}
			}
			echo '	</news>' . "\n";
		}
		echo '</vectra>';
	}

	mysql_free_result($rsData);
	mysql_close($con);
?>
