
<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta http-equiv="Content-Language" content="en-us">
<title>Vectra Bot</title>
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<LINK 
href="http://vectra-bot.org/cmdlist/style.css" type=text/css rel=stylesheet>
<STYLE>

 BODY {
  scrollbar-arrow-color:cccccc;
  scrollbar-shadow-color:ffffff;
  scrollbar-face-color:ffffff;
  scrollbar-highlight-color:cccccc;
  scrollbar-darkshadow-color:cccccc;
.style1 {
	font-size: 18px;
	font-weight: bold;
}
.style3 {
	color: #FF0000;
	font-size: 16px;
	font-weight: bold;
}
.style5 {
	color: #990000
}
.style6 {
	color: #000000;
	font-weight: bold;
}
.style9 {color: #000066}
</STYLE>
</head>

<body>
<div align="center">
  <center>
  <table border="0" width="500" cellspacing="0" cellpadding="0" bgcolor="#EEECEC">
    <tr>
      <td width="33%" background="http://www.vectra-bot.org/cmdlist/top.gif"><img border="0" src="http://www.vectra-bot.org/cmdlist/logorickys.gif" width="750" height="100"></td>
    </tr>
    <tr>
      <td width="33%" background="http://www.vectra-bot.org/cmdlist/middle.gif" valign="top">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="20%">&nbsp;</td>
            <td width="20%">&nbsp;</td>
            <td width="20%">&nbsp;</td>
            <td width="20%">&nbsp;</td>
            <td width="20%">&nbsp;</td>
          </tr>
        </table>
              <table width="95%" border="2" align="center" cellpadding="8" cellspacing="5">
                <tr>
                  <td width="67%" height="269" valign="center" bgcolor="#CCCCCC"><p align="left">
                    <?php
$default	= "x";	// fila som skal inkluderes hvis variabelen er tom.
$directory	= "filer/";		// mappa filene dine ligger i.
$extension	= "php";		// filendingen på filene dine.


/*** SCRIPTET STARTER HER ***************************************************/
/*** (ikke gjør endringer med mindre du vet hva du gjør! =) *****************/

$page = $_GET['p'];

// for å hindre at det inkluderes fra uønskede plasser (stopper hackerne)
if (preg_match('/(http:\/\/|^\/|\.+?\/)/', $page)) echo "Du har ikke tilgang her"; 


elseif (!empty($page))											// sjekke at variabelen ikke er tom.
{
	if (file_exists("$directory/$page.$extension"))				// sjekke om fila eksisterer.
		include("$directory/$page.$extension");					// inkluder fila.
	else														// hvis ikke,
		echo "<h1>Error 404</h1>\n<p>Couldn't find the site. Try again</p>\n";	// skriv en feilmelding.
}
else															// eller,
	include("$directory/$default.$extension");					// inkluder fila som definert som $default.

?>
                    <br>
                  </p>
                    <p align="left"><br>
                  </p>                  </tr>
            </table>            </td>
          </tr>
          <tr>
          </tr>
        </table>      </td>
    </tr><tr>
      <td width="33%" background="http://www.vectra-bot.org/cmdlist/down.gif" height="40">
        <table border="0" width="100%">
          <tr>
            <td width="99%" bordercolor="#FFFFFF">
            <p align="center" class="downtext" style="margin-left: 20">Copyright © 2006-08 Vectra-bot.org. All rights reserved.</td>
            <td width="1%" bordercolor="#FFFFFF" bgcolor="#FFFFFF">
            <p align="center">&nbsp;</td>
          </tr>
        </table>      </td>
    </tr>
  </table>
</div>

<div style="font-size: 0.8em; text-align: center;"><br />
</div>
</body>

</html>
