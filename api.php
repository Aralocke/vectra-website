<?php
function Allowed() {
	return 1;
	$ArrayOfOkayIPs = array('Arconiaprime' => '76.95.120.217', 'JR' => '70.48.240.8');
	foreach($ArrayOfOkayIPs as $Host=>$IP) {
		if ($_SERVER['REMOTE_ADDR'] == $IP) { return 1; }
	}
	return 0;
}
if (Allowed() == 1) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');


	define('DBHOST', 'localhost');
	define('DBPORT', '3306');
	define('DBNAME', 'Vectra');
	define('DBUSER', 'Vectra');
	define('DBPASS', 'cZtXHwvxP7zn2Tms');

/*
 * CREATE TABLE Vectra.Channel_Data (
 * 	channel_id INT NOT NULL ,
 * 	channel_name VARCHAR( 30 ) NOT NULL ,
 * 	channel_event TEXT NOT NULL ,
 * 	channel_site TEXT NOT NULL ,
 * 	channel_auto_stats BOOL NOT NULL ,
 * 	channel_auto_cmb BOOL NOT NULL ,
 * 	channel_auto_clan BOOL NOT NULL ,
 * 	channel_public BOOL NOT NULL ,
 * 	channel_global_ge BOOL NOT NULL ,
 * 	channel_global_rsc BOOL NOT NULL ,
 * 	channel_commands TEXT NOT NULL ,
 * 	PRIMARY KEY ( channel_id ) ,
 * 	UNIQUE ( channel_name )
 * 	) ENGINE = MYISAM 
 */
	if (isset($_POST['Mode'])) { $_Mode = $_POST['Mode']; } 
	elseif (isset($_GET['Mode'])) { $_Mode = $_GET['Mode']; }
	else { $_Mode = 0; }
	if ((isset($_POST['VeRifIEdB0t']) && $_POST['VeRifIEdB0t'] == 'verified') || (isset($_GET['VeRifIEdB0t']) && $_GET['VeRifIEdB0t'] == 'verified')) {
		switch ($_Mode) {
			case 1:					
					$Dbc = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
					if (!$Dbc) { die('ERROR: conencting to database'); }
					else {
						$Network   = mysqli_real_escape_string($Dbc, $_POST['network']);
						$Channel   = mysqli_real_escape_string($Dbc, $_POST['chan']);
						$Event     = mysqli_real_escape_string($Dbc, urldecode($_POST['Event']));
						$Site      = mysqli_real_escape_string($Dbc, urldecode($_POST['Site']));
						$AutoClan  = explode(':', $_POST['Autocmds']);
						$AutoCmb   = mysqli_real_escape_string($Dbc, $AutoClan[0]);
						$AutoStats = mysqli_real_escape_string($Dbc, $AutoClan[2]);					
						$AutoVoice = mysqli_real_escape_string($Dbc, $AutoClan[3]);
						$AutoClan  = mysqli_real_escape_string($Dbc, $AutoClan[1]);
						$Public    = mysqli_real_escape_string($Dbc, $_POST['Public']);
						$vLock     = mysqli_real_escape_string($Dbc, $_POST['vlock']);
						$Rsc       = mysqli_real_escape_string($Dbc, $_POST['Rsc']);
						$Ge        = mysqli_real_escape_string($Dbc, $_POST['Ge']);
						$DefML     = mysqli_real_escape_string($Dbc, urldecode($_POST['Ml']));
						$Commands  = mysqli_real_escape_string($Dbc, $_POST['Commands']);
						$Query = "REPLACE INTO Channel_Data SET 
								  channel_name = '{$Network}{$Channel}',
								  channel_event = '{$Event}',
								  channel_site = '{$Site}',
								  channel_auto_stats = '{$AutoStats}',
								  channel_auto_cmb = '{$AutoCmb}',
								  channel_auto_clan = '{$AutoClan}',
								  channel_auto_voice = '{$AutoVoice}',
								  channel_public = '{$Public}',
								  channel_voicelock = '{$vLock}',
								  channel_global_ge = '{$Ge}',
								  channel_global_rsc = '{$Rsc}',
								  channel_default_ml = '{$DefML}',
								  channel_commands = '{$Commands}'";
						$Query = mysqli_query($Dbc, $Query);
						if (!$Query) { echo 'ERROR: '.mysqli_error($Dbc)."\n"; } 
						echo "RESULT: 1\r\n";
					}
					mysqli_close($Dbc);					
			break;
			case 2:
				$Dbc = mysqli_connect(DBHOST, DBUSER, DBPASS, DBNAME);
				if (!$Dbc) { die('ERROR: conencting to database'); }
				else {
					if (isset($_GET['Channel'])) {
						$Chans = explode(':', $_GET['Channel']);
						foreach ($Chans as $Channel) {
							$Channel = '#'.mysqli_real_escape_string($Dbc, $Channel);
							if (isset($_GET['Network'])) { $Network = mysqli_real_escape_string($Dbc, $_GET['Network']); }
							else { $Network = 'SwiftIRC'; }
							$Query = mysqli_query($Dbc,
									"SELECT * FROM Channel_Data
									 WHERE channel_name = '{$Network}{$Channel}'");
							if (mysqli_num_rows($Query) == 0) { print("RESULT: Not Found {$Channel}\n"); }
							else {							
								$Query = mysqli_fetch_array($Query); $Break = chr(215);
								echo "{$Query['channel_name']}{$Break}{$Query['channel_event']}{$Break}{$Query['channel_site']}{$Break}{$Query['channel_auto_stats']}:{$Query['channel_auto_cmb']}:{$Query['channel_auto_clan']}:{$Query['channel_auto_voice']}{$Break}{$Query['channel_public']}{$Break}{$Query['channel_voicelock']}{$Break}{$Query['channel_global_ge']}{$Break}{$Query['channel_global_rsc']}{$Break}{$Query['channel_default_ml']}{$Break}{$Query['channel_commands']}\n";								
							}
						}
					}//foreach
					echo "RESULT: 1\r\n";
				}			
				mysqli_close($Dbc);
			break;
			default:
				echo "No Mode specified\n";
			break;
		}
	} else die("RESULT: 0\r\n");	
} else echo "You ain't on the list\r\n"; 
?>