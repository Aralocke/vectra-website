<?php
if (!$_GET["p"]) {
	header("Location: ?p=list");
	die();
}
elseif ($_GET["p"] == "list") {
	echo "<pre>\n<strong>These are the possible next skill data options</strong>\n<ul>\n";
	for ($x = 1; $x < 25; $x++) {
		echo "<li><a href=?p=" . $x . ">" . strtolower(skills($x)) . "</a></li>\n";	
	}
	echo "</ul>";
}
elseif ($_GET["p"] < 24 || $_GET["p"] > 0) {
	$file = file_get_contents("http://vectra-bot.net/Skill%20Data/" . skills($_GET["p"]));
	if ($file) {
		echo "
		<table>
		<caption>" . skills($_GET["p"]) . "</caption>
		<thead>
		<tr>
		<th>Item/Monster</th>
		<th>Exp</th>
		</tr>
		</thead>
		<tbody>
		";
		$file = explode("\n", $file);
		for ($x = 0; $x <= (count($file) - 2); $x++) {
			$data = explode("|", $file[$x]);
			echo "<tr><th>" . $data[0] . "</th>";
			echo "<td>" . $data[1] . "</td></tr>";
		}
		echo "
		</tbody>
		<tfoot><tr><td><a href=?p=list>Back to List</a></td></tr></tfoot>
		</table>
		";

	}
	else die("Error reading from file");
}
function skills ($n) {
	$skills = array( "OVERALL", "Attack", "Defence", "Strength", "Hitpoints",
    "Ranged", "Prayer", "Magic", "Cooking", "Woodcutting", "Fletching", "Fishing",
    "Firemaking", "Crafting", "Smithing", "Mining", "Herblore", "Agility",
    "Thieving", "Slayer", "Farming", "Runecraft", "Hunter", "Construction",
    "Summoning" ) ;
	return $skills[$n];
}
?>