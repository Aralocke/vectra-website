<?php
if (!defined('IN_PARSERS') || !IN_PARSERS) {
	exit;
} 
elseif (empty($_GET['switch'])) 
{ 
   print "ERROR: Missing argument `switch`\n";
}
elseif (empty($_GET['search'])) {
	print "ERROR: Missing argument `search`\n";
} 
else {
	$_Link 		= array('http://stats.swiftirc.net', '/lookup/' . $_GET['switch'] . '/' . str_replace('#','',$_GET['search']));
	#$cacheFile 	= "SwiftIRC." . str_replace(' ', '_', $_GET['search']);
	#$src 		= httpCacheSocket("GET", $_Link[0], $_Link[1], $cacheFile, 10, $cOpts, 0);
	$src 		= simplexml_load_file($_Link[0] . $_Link[1]);
	if (!empty($src)) {
		if (isset($src->error)) 
        {
			print "ERROR: $src->error\n";
		}
		elseif ($_GET['switch'] == 'user') 
        { 
    		print "NICKNAME: $src->nick\n";
    		print "REALNAME: $src->realname\n";
    		print "HOSTMASK: $src->hiddenhostname\n";
    		print "IDENT: $src->username\n";
    		print "CONNECTTIME: $src->connecttime\n";
    		print "AWAY: $src->away\n";
			print "AWAYMSG: $src->awaymsg\n";
    		print "ONLINE: $src->online\n";
		}
		elseif ($_GET['switch'] == 'chan') 
        { 
    		print "CHANNEL: $src->channel\n";
    		print "CURRENTUSERS: $src->currentusers\n";
    		print "MAXUSERS: $src->maxusers\n";
    		print "MAXUSERTIME: $src->maxusertime\n";
    		print "TOPIC: " . preg_replace("#(?:\[C\]\d+)#i", null, $src->topic) . "\n";
    		print "TOPICAUTHOR: $src->topicauthor\n";
    		print "TOPICTIME: $src->topictime\n";
		}
	}
}