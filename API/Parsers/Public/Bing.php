<?php

if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}
define('BING_KEY', '') ;

if (empty($_GET['q']))
{
    print 'ERROR: Missing argument &q'.chr(10) ;
}
else
{
    $Sources = array(
        'Web',
        'Image',
        'News',
        'InstantAnswer',
        'RelatedSearch',
        'Video'
    ) ;
    
    $Source = 'Web' ;
    if (!empty($_GET['src']) && in_array($_GET['src'], $Sources))
    {
        $Source = trim($_GET['src']) ;
    }

    $Uri = '/json.aspx?Appid='.BING_KEY.'&query='.urlencode($_GET['q']).'&sources='.$Source ;
    $cacheFile = 'Bing.'.$Source.'.'.urlencode($_GET['q']) ;
    # echo 'LINK: http://api.search.live.net'.$Uri.chr(10);
    $src = httpCacheSocket(
        "GET",
        "http://api.search.live.net",
        $Uri,
        $cacheFile,
        120
    ) ;
    
    if (!is_object($src))
    {
        $results = @json_decode($src) ;
        if ($results == false)
        {
            echo 'PHP: json_decode failure.' . chr(10) ;
        }
        elseif (!is_object($results))
        {
            echo 'ERROR: No search for "'.$_GET['q'].'" found.' . chr(10) ;
        }
        else 
        {
            $title = '' ;
            $mediaurl = '' ;
            $desc = '' ;
            $url = '' ;
            $runtime = '' ;
            $answer = '' ;
            $resource = '' ;
            # print_r($results) ;
            if (isset($results->SearchResponse->$Source))
            {
                $results = $results->SearchResponse->$Source ;
                if (!isset($results->Results))
                {
                    echo 'ERROR: No search for "'.$_GET['q'].'" found.' . chr(10) ;
                }
                else
                {
                    echo 'REAL: ' . $results->Total . chr(10) ;                
                    $results = $results->Results ;
                    echo 'RESULTS: ' . count($results) . chr(10) ;
                    for ($i = 0; $i < count($results); $i++)
                    {   # var_dump($results[$i]) ;
                        $title = (!empty($results[$i]->Title)) ? trim($results[$i]->Title) : trim($results[$i]->SourceTitle) ;
                        switch ($Source)
                        {
                            case "Web":
                                $desc = chr(1) . $results[$i]->Description ;
                                $url = $results[$i]->Url ;
                                $url = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($url) : $url) ;
                                break ;
                            case "Image":
                                $mediaurl = $results[$i]->MediaUrl ;
                                $mediaurl = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($mediaurl) : $mediaurl) ;
                                $url = $results[$i]->Url ;
                                $url = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($url) : $url) ;
                                $desc = chr(1) . $results[$i]->Width.'x'.$results[$i]->Height ;
                                break ;
                            case "News":
                                $resource = chr(1) . $results[$i]->Source ;
                                $desc = chr(1) . $results[$i]->Snippet ;
                                $url = $results[$i]->Url ;
                                $url = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($url) : $url) ;
                                break ;
                            case "InstantAnswer":
                                $answer = chr(1) . $results[$i]->InstantAnswerSpecificData->Encarta->Value ;
                                break ;
                            case "RelatedSearch":
                                $url = $results[$i]->Url ;
                                $url = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($url) : $url) ;
                                break ;
                            case "Video":
                                $url = $results[$i]->PlayUrl ;
                                $url = chr(1) . ((SHORT_LINKS) ? Google::shortUrl($url) : $url) ;
                                $runtime = chr(1) . $results[$i]->RunTime ;
                                break ;
                        }
                        print $i + 1 . ": {$title}{$desc}{$mediaurl}{$url}{$resource}{$runtime}{$answer}\n" ;
                    }
                }
            }
            else
            {
                print "ERROR: No search results found for '{$_GET['q']}'\n" ;
            }
        }
    }
    else
    {
        catchErrorHandler($src) ;
    }
}

?>