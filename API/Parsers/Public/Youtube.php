<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
require_once (CLASS_DIR . 'Youtube.Class.php') ;

if (empty($_GET['q'])) {
    print 'ERROR: Missing argument &q' . chr(10) ;
}
else { 
    $Query = $_GET['q'];
    $Switch = (!empty($_GET['user']) ? 1 : 0) ;
    $Parser = new Youtube_Parser ;
    switch($Switch) { 
        
        case "0":
        $Num = 0;
        if (!empty($_GET['num']) && is_numeric($_GET['num'])) {
            $Num = (int) $_GET['num'] ;
            if ($Num > 10) { $Num = 10; }
            elseif ($Num < 0) { $Num = 1; }
            $Num = $Num - 1;
        }
            $xmldoc = $Parser->searchVideo($Query) ;
            $xmlobj = $Parser->parseXML($xmldoc) ;
            $Results = $Parser->parseXMLfeed($xmlobj, Youtube_Parser::VIDEO_SEARCH) ;
            if (empty($Results) || empty($Results[$Num])) {
            echo 'ERROR: Nothing found for search ' . $Query . chr(10) ;
            }
            else { 
                echo 'TITLE: ' . $Results[$Num]->title . chr(10) ;
                echo 'DURATION: ' . duration($Results[$Num]->length) . chr(10) ;
                echo 'RATING: ' . sprintf("%s %d %d %d", $Results[$Num]->rating['average'], $Results[$Num]->rating['max'],
                    $Results[$Num]->rating['min'], $Results[$Num]->rating['numRaters']) . chr(10) ;
                echo 'VIEWS: ' . $Results[$Num]->views . chr(10) ;
                echo 'AUTHOR: ' . $Results[$Num]->author . chr(10) ;
                echo 'PUBLISHED: ' . duration(time() - $Results[$Num]->published) . chr(10) ;
                echo 'UPDATED: ' . duration(time() - $Results[$Num]->updated) . chr(10) ;
                echo 'CATEGORIES: ' . implode(', ', $Results[$Num]->categories) . chr(10) ;
                echo 'KEYWORDS: ' . implode(', ', $Results[$Num]->keywords) . chr(10) ;
                $Link = $Results[$Num]->link;
                echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Link) : $Link) . chr(10) ;
            }
        break;
        
        case "1":
            $xmldoc = $Parser->searchUser($Query) ;
            if ($xmldoc == 'User not found') { 
                echo 'ERROR: Nothing found for search ' . $_GET['q'] . chr(10) ;
            }
            else { 
            $xmlobj = $Parser->parseXML($xmldoc) ;
            $Results = $Parser->parseXMLfeed($xmlobj, Youtube_Parser::USER_SEARCH) ;
                echo 'NAME: ' . $Results->name . chr(10) ;
                echo 'JOINED: ' . date('M-d-y', $Results->joined) . chr(10) ;
                echo 'LASTSEEN: ' . date('M-d-y', $Results->lastSeen) . chr(10) ;
                echo 'SUBSCRIBERS: ' . $Results->subscribers . chr(10) ;
                echo 'VIEWS: ' . $Results->views . chr(10) ;
                echo 'FAVORITES: ' . $Results->favorites . chr(10) ;
                echo 'CONTACTS: ' . $Results->contacts . chr(10) ;
                echo 'UPLOADS: ' . $Results->uploads . chr(10) ;
                echo 'LOCATION: ' . $Results->location . chr(10) ;
                echo 'CATEGORY: ' . $Results->category . chr(10) ;
                echo 'FIRSTNAME: ' . $Results->firstName . chr(10) ; 
            }
        break;
    }
}

?>