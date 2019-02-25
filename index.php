<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:spry="http://ns.adobe.com/spry">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<meta name="description" content="Home of the RuneScape MMORPG Chat Room Statistics Bot">
<meta name="keywords" content="runescape jagex IRC SwiftIRC skype msn msn aim yahoo facebook twitter world of warcraft">

<title>Vectra || IRC based MMORPG bot</title>
<link rel="shortcut icon" href="images/favicon.ico" />
<link rel="stylesheet" href="/css/base.css" media="screen" />
<link rel="stylesheet" href="/css/SpryAccordion.css" media="screen" />
<link rel="stylesheet" href="/css/SpryTooltip.css" media="screen" />
<link rel="stylesheet" href="/css/SpryValidationTextField.css" media="screen" />
<link rel="stylesheet" href="/css/SpryValidationTextArea.css" media="screen" />
<script type="text/javascript" src="/includes/SpryCore.js"></script>
<script type="text/javascript" src="/includes/Vectra.js"></script>
<script type="text/javascript" src="/includes/sortables.js"></script>
</head>
<body id="body">
<div id='loading'>
  <div id="imgLoader"><img src="images/ind.gif" height="32px" width="32px" alt="loading.."  /></div>
  <div id='textLoader'><strong>Please wait</strong> until our data is implemented by your browser. <span>~ Vectra staff</span></div>
</div>
<!-- End Loading -->
<div id="container">
  <div id="header">
    <div id="logo"></div>
  </div>
  <div id="AdBox" style="text-align:center;margin:0 0 2em 0;display:block;">
	<div class="AdVisual" style="">
    <script type="text/javascript"><!--
        google_ad_client = "ca-pub-0184980340825175";
        /* Vectra Current Site Ad Unit */
        google_ad_slot = "0391467593";
        google_ad_width = 728;
        google_ad_height = 90;
        //-->
    </script>
    <script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
    </div>
    <div class="Adtext" style="display:block;text-align:right;margin:0 auto;">
    	<a href="/forum/viewtopic.php?f=24&t=645" style="padding:0.5em 1em 0 0;font-size:10px;color:#424243;">Report Bad Ad</a>
    </div>
  </div>
  <!-- End logo/adbox -->
  <div id="datawrapper" class="transparent_class" style="display:none;">
    <div id="sort1" class="groupWrapper">
      <div id="news" class="groupItem">
        <div class="itemHeader">News<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent">
          <div spry:region="dsNews" class="SpryHiddenRegion" id="dsNewsRegion">
            <div id="Acc1" class="Accordion">
              <div class="AccordionPanel" spry:repeat="dsNews">
                <div class="AccordionPanelTab">{dsNews::header} - <span>{dsNews::Date}</span></div>
                <div class="AccordionPanelContent">
                  <div class="accordionConterborder"><strong>{dsNews::header}!</strong>
                    <p class="intro">{dsNews::content}</p>
                  </div>
                  <table cellpadding="0" cellspacing="5" border="0">
                    <tr>
                      <td width="20">By:</td>
                      <td width="50">{dsNews::poster}</td>
                      <td width="20">Date:</td>
                      <td width="100">{dsNews::Date}</td>
                    </tr>
                    <tr>
                      <td><img src="images/icon-comments.png" alt="{dsNews::comments} comments"  /></td>
                      <td>{dsNews::comments}</td>
                      <td><img src="images/icon-link.png" alt="Read More." /></td>
                      <td><a href="{dsNews::link}" class="readmore">Read More.</a></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <div id="top5" class="groupItem">
        <div class="itemHeader">Vectra Staff!<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent"> <strong>A good bot needs an awsome crew!</strong>
          <p class="intro">A good managed bot is healthy bot. Here at <b>Vectra</b> we recruited top coders to make sure the bot gives you the information you need! These are the people that make it all happen:</p>
          <div class="cmdwrapper"> <b>Owners:</b>
            <p class="desc">Xotick, Jeffreims, Redzzy, Arconiaprime</p>
          </div>
          <div class="cmdwrapper"> <b>Adminstrators:</b>
            <p class="desc">[Sooth], IEP, Newkronic, and JoshR</p>
          </div>
          <div class="cmdwrapper"> <b>Web Adminstrators:</b>
            <p class="desc">V1</p>
          </div>
          <div class="cmdwrapper"> <b>Helpers:</b>
            <p class="desc">`Tim</p>
          </div>
          <p class="intro">Vectra wouldn't be possible without this awsome crew, thank you!</p>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <p>&nbsp;</p>
    </div>
    <div id="sort2" class="groupWrapper">
      <div id="login" class="groupItem">
        <div class="itemHeader">Login<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent">
          <form method="POST" action="./forum/login.php">
            <table cellpadding="0" cellspacing="0" border="0" class="login">
              <tr>
                <td rowspan="3"><img src="images/login.png" width="41px" height="53px" alt="login" /></td>
                <td><input class="post" type="text" name="username" /></td>
              </tr>
              <tr>
                <td><input class="post" type="password" name="password" /></td>
              </tr>
              <tr>
                <td><input type="checkbox" name="autologin" style="display:none;" />
                  <input type="submit" value="Submit" name="login" /></td>
              </tr>
            </table>
          </form>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <div id="Menu" class="groupItem">
        <div class="itemHeader">Menu<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent">
          <table cellpadding="0" cellspacing="0" border="0" class="menu">
            <tr>
              <td width="33%" align="center"><a href="http://forum.vectra-bot.net/" onmouseover="document.getElementById('forumImg').src ='images/forum_over.png';" onmouseout="document.getElementById('forumImg').src ='images/forum.png';"><img src="images/forum.png" border="0" width="63px" height="63px" id="forumImg" alt="Go to our Forum" /><strong style="margin-top:15px;">Forum</strong></a></td>
              <td width="33%" align="center"><a href="#" onmouseover="document.getElementById('adminImg').src ='images/admincontrols_over.png';" onmouseout="document.getElementById('adminImg').src ='images/admincontrols.png';"><img src="images/admincontrols.png" border="0" width="63px" height="64px" id="adminImg" alt="Go to AdminControls" /><strong style="margin-top:15px;">Admin Controls</strong></a></td>
              <td width="33%" align="center"><a href="#" onclick="displayContact();" onmouseover="document.getElementById('contactImg').src ='images/contact_over.png';" onmouseout="document.getElementById('contactImg').src ='images/contact.png';"><img src="images/contact.png" border="0" width="52px" height="62px" id="contactImg" alt="Contact us" /><strong style="margin-top:17px;">Contact</strong></a></td>
            </tr>
          </table>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <div id="welcome" class="groupItem">
        <div class="itemHeader">Welcome to Vectra-bot.net<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent"> <strong>Welcome to Vectra-bot.net!</strong>
          <p class="intro">Vectra is a well known MMORPG bot currently running on the IRC network SwiftIRC and is also available on MSN. The project was started in 2006 and after many days, months and years of developing, merging with Terrorserv, recoding and hard work, Vectra has become a great IRC bot with tons of useful commands.</p>
          <p style="margin-top:10px;color:#707070;">If you want a great 24/7 IRC Bot in your channel, type: <span>/invite Vectra #&lt;channel name&gt;</span> and start using it. You can also add it on MSN, add <span>bot@vectra-bot.org</span> and use the commands in a MSN conversation</p>
          <p style="margin-top:10px;color:#707070;">Join <span>#Vectra</span> at irc.SwiftIRC.net to get support or answers to your questions!
            You can also email our support email: <a href="mailto:vectra@vectra-bot.net">vectra@vectra-bot.net</a></p>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <div id="crew" class="groupItem" style="display:NONE;">
        <div class="itemHeader">Vectra's awesome crew<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent"> Todo:
          <ul>
            <li>Add top 5</li>
            <li>create crew list</li>
          </ul>
        </div>
        <div class="itemFooter">&nbsp;</div>
      </div>
      <p>&nbsp;</p>
    </div>
    <div id="sort3" class="groupWrapper">
      <div id="commands" class="groupItem">
        <div class="itemHeader">Commands list<a href="#" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
        <div class="itemContent">
          <div class="cmdtop"> Type of commands: </div>
          <div class="cmdtop">
            <div style="height:40px;">
              <div class="V1" id="cb_rs" onclick="Spry.Utils.CommandFilter({runescape:true});">&nbsp;</div>
              <div class="checkboxlable">RuneScape</div>
              <div class="V1" id="cb_nrs" onclick="Spry.Utils.CommandFilter({nonrunescape:true});">&nbsp;</div>
              <div class="checkboxlable">Non RS</div>
              <div class="V1" id="cb_fun" onclick="Spry.Utils.CommandFilter({fun:true});">&nbsp;</div>
              <div class="checkboxlable">Fun</div>
              <div class="V1" id="cb_brl" onclick="Spry.Utils.CommandFilter({botrelated:true});">&nbsp;</div>
              <div class="checkboxlable">Bot related</div>
              <div class="V1" id="cb_oth" onclick="Spry.Utils.CommandFilter({other:true});">&nbsp;</div>
              <div class="checkboxlable">Other</div>
            </div>
          </div>
          <div spry:region="dsVectra" class="SpryHiddenRegion">
            <div spry:repeat="dsVectra" class="cmdwrapper"> <span class="command">{command}</span> <span class="desc">{desc}</span> <span class="response">{response}</span> </div>
          </div>
          <div spry:detailregion="vectraInfo" spry:if="{ds_UnfilteredRowCount} > 0" class="cmdwrapper SpryHiddenRegion" align="center">
            <div spry:if="{ds_PageTotalItemCount} > {ds_PageSize}" class="pageControl">
              <div> <a spry:if="{ds_CurrentRowNumber} >= 1" href="#" onclick="dsVectra.previousPage();return false;" class="bg"><img src="images/left.png" border="0" height="6px" width="4px" title="Prev. page" alt="Prev. page" /></a>
                <div spry:if="{ds_CurrentRowNumber} <= 0" class="bg"><img src="images/left_inactive.png" border="0" height="6px" width="4px" title="Prev. page" alt="Prev. page" /> </div>
                <div spry:repeatchildren="vectraInfo"> <a class="bg" spry:if="{ds_CurrentRowNumber} != {ds_RowNumber}" href="#" onclick="dsVectra.goToPage('{ds_PageNumber}');return false;">{ds_PageNumber}</a>
                  <div spry:if="{ds_CurrentRowNumber} == {ds_RowNumber}" class="bg">{ds_PageNumber}</div>
                </div>
                <div> <a class="bg" spry:if="{ds_PageLastItemNumber} < {ds_PageTotalItemCount}" href="#" onclick="dsVectra.nextPage();return false;"><img src="images/right.png" border="0" height="6px" width="4px" title="Next page" alt="Next page" /></a>
                  <div spry:if="{ds_PageLastItemNumber} >= {ds_PageTotalItemCount}" class="bg"><img src="images/right_inactive.png" border="0" height="6px" width="4px" title="Next page" alt="Next page" /></div>
                </div>
              </div>
            </div>
          </div>
          <div align="center">
            <div class='commandList' onclick="displayInformation()">
              <p>Click here for the full command list. <br/>
                <span>(click here)</span></p>
            </div>
          </div>
        </div>
        <div class="itemFooter"></div>
      </div>
      <p>&nbsp;</p>
    </div>
  </div>
  <!-- END DATA -->
</div>
<!-- lightbox wrapper !IMPORTANT! -->
<div id="isFloat" class="lightbox" style="display:none;"></div>
<!-- /end wrapper -->
<div id="contactRegion" class="groupItem SpryHiddenRegion center" spry:region="dsCommands" style="display:none;">
  <div class="itemHeader">Contact<a href="javascript:CloseInformation();" class="closeEl"><img src="images/widget_close.png" border='0' alt="Close this pannel" id="closeContact" /></a></div>
  <div class="itemContent">
    <div spry:if="mailcheck() == 1">
      <form name="contact" id="contactform" method="post" action="mail/process.php" onsubmit="return validateonsubmit(this);">
        <table cellpadding="0" cellspacing="0" border="0" class="login" style="margin-left:8px;">
          <tr id="validName">
            <td>Name:</td>
            <td><input class="post" type="text" name="name" />
              <div class="textfieldRequiredMsg">The value is required.</div></td>
          </tr>
          <tr id="validEmail">
            <td>Email:</td>
            <td><input class="post" type="text"  name="email" />
              <div class="textfieldRequiredMsg">The value is required.</div>
              <div class="textfieldInvalidFormatMsg">Invalid format.</div></td>
          </tr>
          <tr id="validSubject">
            <td>Subject:</td>
            <td><input class="post" type="text"  name="subject" />
              <div class="textfieldRequiredMsg">The value is required.</div></td>
          </tr>
          <tr id="validMessage">
            <td>message:</td>
            <td>Enter at least 20 characters:<br />
              <textarea class="textarea" name="content" cols="23" rows="10"></textarea>
              <div class="textareaRequiredMsg">The value is required.</div>
              <div class="textareaMinCharsMsg">The min number not met.</div>
              <div>Amount of characters typed: <span id="Counttextarea_min_chars">&nbsp;</span></div></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><input type="submit" value="SUBMIT" name="send" /></td>
          </tr>
        </table>
      </form>
    </div>
    <p spry:if="mailcheck() == 0" class="intro">You can only send <b>1</b> message per day, please wait till our services contact you, thanks for understanding.<br />
      <span>~ Vectra Team</span></p>
  </div>
  <div class="itemFooter">&nbsp;</div>
</div>
<div id="megaList" style="display:none;">
  <div class="largeHeader">Total commandlist <a href="#" class="closeEl" style="right:16px;" onclick="CloseInformation();"><img src="images/widget_close.png" border='0' alt="Close this pannel" /></a></div>
  <div spry:region="dsSMCMD" id="CommandRegion" class="largeCenter SpryHiddenRegion">
    <div class="groupLargeWrapper">
      <ul class="row">
        <li spry:repeat="dsSMCMD" class="cell15" spry:if="{ds_RowID} < 15">
          <div class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</div>
        </li>
        <li spry:repeat="dsSMCMD" class="cell30" spry:if="{ds_RowID} < 30 && {ds_RowID} > 15" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
        <li spry:repeat="dsSMCMD" class="cell45" spry:if="{ds_RowID} < 45 && {ds_RowID} > 30" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
        <li spry:repeat="dsSMCMD" class="cell60" spry:if="{ds_RowID} < 60 && {ds_RowID} > 45" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
        <li spry:repeat="dsSMCMD" class="cell75" spry:if="{ds_RowID} < 75 && {ds_RowID} > 60" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
        <li spry:repeat="dsSMCMD" class="cell100" spry:if="{ds_RowID} < 100 && {ds_RowID} > 75" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
        <li spry:repeat="dsSMCMD" class="cell200" spry:if="{ds_RowID} < 200 && {ds_RowID} > 100" ><span class="command trigger" onmouseover="dsSMCMD.setCurrentRow('{ds_RowID}');">{command}</span></li>
      </ul>
    </div>
  </div>
</div>
<div class="largeFooter">&nbsp;</div>
</div>
<!-- The tooltip container. The id is used in the constructor. -->
<div spry:detailregion="dsSMCMD" id="tooltip" class="bubble SpryHiddenRegion" style="display:none;">
  <div class="rounded">
    <blockquote>
      <p><strong>Command:</strong> {command}</p>
      <p><strong>Desc:</strong> {desc}</p>
      <p><strong>Response:</strong> {response}</p>
    </blockquote>
  </div>
</div>
<font style="position: absolute;overflow: hidden;height: 0;width: 0"> </font>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17293279-2']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</body>
</html>