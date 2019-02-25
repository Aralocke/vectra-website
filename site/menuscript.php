<?php
// Script by Thomas / Active

if (!isset($_GET['p']))
{
	echo "	<ul>
			<li id='current'><a href='?p=idx'>Home </a></li>
			<li><a href='?p=about'>About </a></li>
			<li><a href='http://forum.vectra-bot.org/'>Forum </a></li>
			<br />
			<br />
			<li><a href='?p=commands'>Commands </a></li>
			<li><a href='?p=acommands'>Admin </a></li>			
		  </ul>";
}

if ($_GET['p'] == "idx")
{
	echo "	<ul>
			<li id='current'><a href='?p=idx'>Home </a></li>
			<li><a href='?p=about'>About </a></li>
			<li><a href='http://forum.vectra-bot.org/'>Forum </a></li>
			<br />
			<br />
			<li><a href='?p=commands'>Commands </a></li>
			<li><a href='?p=acommands'>Admin </a></li>			
		  </ul>";
}

if ($_GET['p'] == "about")
{
	echo "	<ul>
			<li><a href='?p=idx'>Home </a></li>
			<li id='current'><a href='?p=about'>About </a></li>
			<li><a href='http://forum.vectra-bot.org/'>Forum </a></li>
			<br />
			<br />
			<li><a href='?p=commands'>Commands </a></li>
			<li><a href='?p=acommands'>Admin </a></li>			
		  </ul>";
}

if ($_GET['p'] == "commands")
{
	echo "	<ul>
			<li><a href='?p=idx'>Home </a></li>
			<li><a href='?p=about'>About </a></li>
			<li><a href='http://forum.vectra-bot.org/'>Forum </a></li>
			<br />
			<br />
			<li id='current'><a href='?p=commands'>Commands </a></li>
			<li><a href='?p=acommands'>Admin </a></li>			
		  </ul>";
}

if ($_GET['p'] == "acommands")
{
	echo "	<ul>
			<li><a href='?p=idx'>Home </a></li>
			<li><a href='?p=about'>About </a></li>
			<li><a href='http://forum.vectra-bot.org/'>Forum </a></li>
			<br />
			<br />
			<li><a href='?p=commands'>Commands </a></li>
			<li id='current'><a href='?p=acommands'>Admin </a></li>			
		  </ul>";
}
?>