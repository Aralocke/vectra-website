<?php
if (!$_GET["password"] or $_GET["password"] != "vectraownsikick") { die("Incorrect Password"); }
//$dbase = "BotData";
//$dbhost = "127.0.0.1";
//$dbuser = "root";
$socket = mysql_connect($dbhost, $dbuser, $dbpass);
if (!$socket) { die("Error connect socket"); }
mysql_select_db($dbase, $socket);
$query = "SELECT * FROM `BotStats`";

if (!mysql_query($query, $socket))
{
	if (!$_GET["current"] or !is_numeric($_GET["current"])) { die("No current bot count specified"); }
    $query = "CREATE TABLE `BotStats` (`P_Id` int NOT NULL AUTO_INCREMENT,`Channels` INT, `Users` INT, PRIMARY KEY (P_Id))";
    mysql_query($query, $socket);
    for ($x = 0; $x < $_GET["current"]; $x++)
    {
        $query = "INSERT INTO `BotStats` (Channels, Users) VALUES ('1', '1')";
        echo "INSERT INTO `BotStats` (Channels, Users) VALUES ('1', '1')";
        mysql_query($query, $socket);
    }
    if (is_numeric($_GET["bot"]) && $_GET["users"] && $_GET["chans"])
    {
        $query = "UPDATE BotStats SET Channels='" . $_GET["chans"] . "', Users='" . $_GET["users"] .
            "' WHERE P_ID='" . $_GET["bot"] . "'";
            echo $query;
        mysql_query($query, $socket);
    }
}
if (is_numeric($_GET["bot"]) && $_GET["users"] && $_GET["chans"])
{
    $query = "UPDATE BotStats SET Channels='" . $_GET["chans"] . "', Users='" . $_GET["users"] .
            "' WHERE P_ID='" . $_GET["bot"] . "'";
            echo $query;
    mysql_query($query, $socket);
} elseif ($_GET["bot"] == "all")
{
    $query = "SELECT * FROM `BotTotal`";
    if (!mysql_query($query, $socket))
    {
        $query = "CREATE TABLE `BotTotal` (`Channels` INT)";
        mysql_query($query, $socket);
        $query = "INSERT INTO `BotTotal` (Channels) VALUES ('1')";
        mysql_query($query, $socket);

        if ($_GET["chans"])
        {
            $query = "UPDATE `BotTotal` SET Channels='" . $_GET["chans"] . "'";
            mysql_query($query, $socket);
        }
    } elseif ($_GET["bot"] == "all" && is_numeric($_GET["chans"]))
    {
        $query = "UPDATE `BotTotal` SET Channels='" . $_GET["chans"] . "'";
        mysql_query($query, $socket);
    }
}
mysql_close($socket);


?>