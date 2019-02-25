<pre>
<?php
if (isset($_GET['keyFor'])) {
	die("Key would be: ".generateKey($_GET['keyFor'])."\n");
}
$Key = generateKey();
echo "Generated key: {$Key}\n";
$Interface = NULL;
if (isset($_GET['validateLogin'])) { 
	$Interface = 'validateLogin'; 
}
elseif (isset($_GET['checkAPIkey'])) { 
	$Interface = 'checkAPIkey'; 
}
if ($Interface == NULL) {
	die("No API Int specified\n");	
}
@define('DEBUG', (isset($_GET['debug']) && $_GET['debug'] == 'true')?1:0);
@define('BUG_FORUM_ID', 14);
@define('STAFF_FORUM_ID', 17);
@define('FORUM_ACC_USER', '');
@define('FORUM_ACC_UID',  '');
@define('FORUM_ACC_PASS', '');
if (DEBUG) { 
	error_reporting(E_ALL);
	ini_set('display_errors', DEBUG);
}
require("../forum/config.php");
define('IN_PHPBB', true);
$phpbb_root_path = '../forum/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);
print_r($config);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
if (!PHPBB_INSTALLED) { die("PHPbb3 is not installed or there are configuration issues"); }
$Result = $db->sql_query("SELECT api_uid, api_uname, api_uip FROM api_keys WHERE api_key = '{$Key}'");
if ($Result->num_rows == 1) {
	echo 'Interface: '.$Interface."\n";
	$Object = $Result->fetch_object();
	$db->sql_freeresult();
	$_Uid   = $Object->api_uid;
	$_Uip   = $Object->api_uip;
	$_Uname = $Object->api_uname;
	if ($Interface == 'checkAPIkey') {
		echo "STATUS: API Key found for user: {$_Uname}@{$_Uip}\n";
	}
	elseif ($Interface == 'validateLogin') {
		$User     = NULL;
		$Password = NULL;
		if (!empty($_POST['auser'])) {
			$User     = $db->sql_escape(trim($_POST['auser']));
		}
		if (!empty($_POST['apass'])) {
			$Password = $db->sql_escape(trim($_POST['apass']));
		}
		if ($User != NULL && $Password != NULL) {			
			$Validation = validatePass($User, $Password);
			if (!$Validation) { echo "STATUS: nologin\n"; }
			else { 
				$Result = $db->sql_query("SELECT * FROM api_data WHERE api_uid = '{$Validation->api_uid}'");
				if ($Result == FALSE) { 
					$db->sql_freeresult();
					echo "STATUS: nologin\n";
				}
				else { 
					$Object = $Result->fetch_object();
					$db->sql_freeresult();
					echo "STATUS: {$User} {$Object->api_access_level} {$Object->api_greet}\n"; 
				}
			}
		}
		else { echo "ERROR: Username or password not found.\n"; }
	}
	if ($Interface == 'postBugReport') {
		$Message = NULL;
		$Subject = NULL;
		if ($Message != NULL && $Subject != NULL) {
			$Post_TopicID = NULL;
			$message_parser = new parse_message();		
			$message_parser->message = $Message;
			$Post_Data = postArray($Post_TopicID, $Subject, $Message, NULL, $message_parser->bbcode_uid);
			$Query = array();
			foreach ($Post_Data as $Key => $Data) {
				$Query[] = "{$Key} = '{$Data}'";
			}
			$db->sql_query("INSERT INTO phpbb3_posts SET ".implode(', ', $Query));
			$Result = $db->sql_query("SELECT * FROM phpbb3_posts ORDER BY post_id DESC");
			$Object = $Result->fetch_object();
			$db->sql_freeresult();
			$PostID = $Object->post_id;
			$Topic_Data = topicArray($Object->post_subject, $PostID, FORUM_ACC_USER, 'CC6600', $Object->post_id, $Object->poster_id, FORUM_ACC_USER, 'CC6600', $Object->post_subject);
			$Query = array();
			foreach ($Topic_Data as $Key => $Data) {
				$Query[] = "{$Key} = '{$Data}'";
			}
			$db->sql_query("INSERT INTO phpbb3_topics SET ".implode(', ', $Query));
			$Result = $db->sql_query("SELECT topic_id FROM phpbb3_topics ORDER BY topic_id DESC");
			$Object = $Result->fetch_object();
			$db->sql_freeresult();
			$db->sql_query("UPDATE phpbb3_posts SET topic_id = '{$Object->topic_id}' WHERE post_id = '{$PostID}'");
			$db->sql_query("UPDATE phpbb3_forums SET forum_topics_real = forum_topics_real + 1, forum_posts = forum_posts + 1, forum_topics = forum_topics + 1, forum_last_post_id = '{$PostID}', forum_last_post_subject = '{$Subject}', forum_last_post_time = {$Post_Data['post_time']}, forum_last_poster_id = '".FORUM_ACC_UID."', forum_last_poster_name = '".FORUM_ACC_USER."', forum_last_poster_colour = 'CC6600' WHERE forum_id = 14");
			echo "STATUS: {$Object->topic_id} {$PostID}\n";
		}
	}
	if (DEBUG) { 
		echo "DEBUG: printing out \$_POST\n"; print_r($_POST);
	}
}
else { echo "You do not have an API key ... oh well.\n"; }
if (isset($db)) { 
	$db->_sql_close(); 
}

###############################
####    Functions
function postArray($Post_TopicID, $Post_Subject, $Post_Text, $Post_BitField, $Post_BBcode_uid) {
	$Post = array();
	$Post['post_id'] = 0;
	$Post['forum_id'] = BUG_FORUM_ID;
	$Post['poster_ip'] = $_SERVER['REMOTE_ADDR'];
	$Post['post_time'] = time();
	$Post['poster_id'] = FORUM_ACC_UID;
	$Post['post_subject'] = $Post_Subject;
	$Post['post_text'] = $Post_Text;
	$Post['post_checksum'] = md5($Post_Text);
	$Post['bbcode_bitfield'] = NULL;
	$Post['bbcode_uid'] = $Post_BBcode_uid;
	return $Post;
}
function topicArray($Title, $First_ID, $First_Name, $First_Colour, $Last_Post_ID, $Last_ID, $Last_Name, $Last_Colour, $Last_Subject) {
	$Topic = array();
	$Topic['topic_id'] = 0;
	$Topic['forum_id'] = BUG_FORUM_ID;
	$Topic['topic_title'] = $Title;
	$Topic['topic_poster'] = FORUM_ACC_UID;
	$Topic['topic_time'] = time();
	$Topic['topic_first_post_id'] = $First_ID;
	$Topic['topic_first_poster_name'] = $First_Name;
	$Topic['topic_first_poster_colour'] = $First_Colour;
	$Topic['topic_last_post_id'] = $Last_Post_ID;
	$Topic['topic_last_poster_id'] = $Last_ID;
	$Topic['topic_last_poster_name'] = $Last_Name;
	$Topic['topic_last_poster_colour'] = $Last_Colour;
	$Topic['topic_last_post_subject'] = $Last_Subject;
	$Topic['topic_last_post_time'] = time();
	$Topic['topic_last_view_time'] = time();
	return $Topic;
}
function validatePass($User, $Password) {
	global $db;
	$Result = $db->sql_query("SELECT user_password, api_uid FROM phpbb3_users WHERE `username` = '{$User}'");
	if ($Result == FALSE) {
		$db->sql_freeresult();
		return FALSE;	
	}
	else {
		$Object = $Result->fetch_object();
		$Result->free_result();
		if (phpbb_check_hash($Password, $Object->user_password)) {
			return $Object;	
		} else { return FALSE; }
	}
}
function generateKey($IP = NULL) {
	if ($IP == NULL) {
		$ConnIp = substr($_SERVER['REMOTE_ADDR'], 0, strpos($_SERVER['REMOTE_ADDR'], '.'));
		$ConnIp %= $ConnIp[1];
		$Key = base64_encode($_SERVER['REMOTE_ADDR']);
	}
	else {
		$ConnIp = substr($IP, 0, strpos($IP, '.'));
		$ConnIp %= $ConnIp[1];
		$Key = base64_encode($IP);
	}
	for ($x = (int)$ConnIp; $x > 0; $x--) {
		$Key = md5($Key);
	}
	return $Key;
}

?>