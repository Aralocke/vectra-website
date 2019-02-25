<?php
if (!defined('IN_PARSERS') || !IN_PARSERS)
{
    exit ;
}

define ('G_WEB', 1) ;
define ('G_IMAGE', 2) ;
define ('G_SITE', 3) ;
define ('G_TRANSLATE', 4) ;
define ('G_CALC', 5) ;
define ('G_CONVERT', 6) ;
define ('G_MAP', 7) ;
define ('G_FIGHT', 8) ;

$Switch = null ;
if (!empty($_GET['s']))
{   
    # determine the switch
    $Switch = (int)(trim($_GET['s'])) ;
}
if (empty($Switch))
{
    echo 'ERROR: Missing arguement &s' . chr(10) ;
}
else 
{
    $Search = null ;
    $To     = null ;
    $From   = null ;
    $Amount = null ; 
    $Site   = null ;
    $String = null ;
    $Start  = null ;
    $End    = null ;
    
    switch ($Switch)
    {
        case G_WEB:
            # Requires: &q
            if (empty($_GET['q']))
            {
                echo 'ERROR: Missing arguement &q' . chr(10) ;
            }
            else
            {
                $Search = trim($_GET['q']) ;
                $Result = Google::search($Search) ;
                if (empty($Result->responseData->results))
                {
                    echo 'ERROR: Search returned no result for "' . $Search . '"' . chr(10) ;
                }
                else
                {
                    $Results = $Result->responseData->results ;
					$Url = $Result->responseData->cursor->moreResultsUrl;
                    echo 'SEARCH: ' . $Search . chr(10) ;
                    echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url) : $Url) . chr(10) ;
                    echo 'RESULTS: ' . $Result->responseData->cursor->estimatedResultCount . chr(10) ;
                    $Count = 1 ;
                    foreach ($Results as $Search)
                    {
						$Url = $Search->url;
                        echo $Count . ': ' . ((SHORT_LINKS) ? Google::shortUrl($Url) : $Url) . chr(7) . $Search->titleNoFormatting . chr(7) . htmlfree($Search->content) . chr(10) ;
                        $Count++ ;
                    }
                }
            }
        break;
        case G_FIGHT:
            # Requires: &q
            if (empty($_GET['q']))
            {
                echo 'ERROR: Missing arguement &q' . chr(10) ;
            }
            elseif (empty($_GET['q2']))
            {
                echo 'ERROR: Missing arguement &q2' . chr(10) ;
            }
            else
            {
                $SearchA = trim($_GET['q']) ;
                $SearchB = trim($_GET['q2']) ;
                $ResultA = Google::search($SearchA) ;
                $ResultB = Google::search($SearchB) ;
                if (empty($ResultA->responseData->results))
                {
                    echo 'ERROR: Search returned no result for "' . $SearchA . '"' . chr(10) ;
                }
                elseif (empty($ResultB->responseData->results))
                {
                    echo 'ERROR: Search returned no result for "' . $SearchB . '"' . chr(10) ;
                }
                else {                 
                    echo 'RESULTS: ' . $ResultA->responseData->cursor->estimatedResultCount . ' ' 
                        . $ResultB->responseData->cursor->estimatedResultCount . chr(10) ;  
                }
            }
        break;
        case G_IMAGE:
            # Requires: &q            
            if (empty($_GET['q']))
            {
                echo 'ERROR: Missing arguement &q' . chr(10) ;
            }
            else
            {
                $Search = trim($_GET['q']) ;
                $Result = Google::image($Search) ;
                if (empty($Result->responseData->results))
                {
                    echo 'ERROR: Search returned no images for "' . $Search . '"' . chr(10) ;
                }
                else
                {
                    $Results = $Result->responseData->results ;
					$Url = $Result->responseData->cursor->moreResultsUrl ;
                    echo 'SEARCH: ' . $_GET['q'] . chr(10) ;
                    echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url) . chr(10) ;
                    echo 'RESULTS: ' . $Result->responseData->cursor->estimatedResultCount . chr(10) ;
					$Count = 1 ;
                    foreach ($Results as $Search)
                    {
						$Url = $Search->url;
                        echo $Count . ': ' . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url) . chr(7) . $Search->titleNoFormatting . chr(7) . $Search->width . 'x' . $Search->height . chr(7) . htmlfree($Search->contentNoFormatting) . chr(10) ;
						$Count++ ;
                    }
                }
            }
        break;
        case G_SITE:
            # Requires: &q &site
            if (empty($_GET['q']) || empty($_GET['site']))
            {
                echo 'ERROR: Missing arguement &q &site' . chr(10) ;
            }
            else
            {
                $Search = trim($_GET['q']) ;
                $Site = trim($_GET['site']) ;
                $Result = Google::search($Search, $Site) ;
                if (empty($Result->responseData->results))
                {
                    echo 'ERROR: Search returned no result for "' . $Search . '"' . chr(10) ;
                }
                else
                {
                    $Results = $Result->responseData->results ;
					$Url = $Result->responseData->cursor->moreResultsUrl ;
                    echo 'SEARCH: ' . $Search . chr(10) ;
                    echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url) . chr(10) ;
                    echo 'RESULTS: ' . $Result->responseData->cursor->estimatedResultCount . chr(10) ;
                    $Count = 1 ;
                    foreach ($Results as $Search)
                    {
						$Url = $Search->url;
                        echo $Count . ': ' . ((SHORT_LINKS) ? Google::shortUrl($Url):$Url) . chr(7) . $Search->titleNoFormatting . chr(7) . htmlfree($Search->content) . chr(10) ;
                        $Count++ ;
                    }
                }
            }
        break;
        case G_TRANSLATE:
            # Requires: &from &to &string
            if (empty($_GET['to']))
            {
                echo 'ERROR: Missing argument &to' . chr(10) ;
            } 
            elseif (empty($_GET['string']) && empty($_POST['string']))
            {
                echo 'ERROR: Missing argument &string' . chr(10) ;
            }
            else
            {   
                $String = (!empty($_GET['string'])) ? trim($_GET['string']) : trim($_POST['string']) ;
                if (Google::shortLang($_GET['to']) or Google::language($_GET['to']))
                {
                    $From = ((empty($_GET['from'])) ? '' : $_GET['from']) ;
                    if (empty($From) || (Google::shortLang($_GET['to']) or Google::language($_GET['to'])))
                    {
                        $Object = Google::translate($String, $_GET['to'], $From) ;
                        #var_dump($Object) ;
                        if (!empty($Object->responseDetails))
                        {
                            echo 'ERROR: ' . $Object->responseDetails . chr(10) ;
                        }
                        else 
                        {
                            echo 'TO: ' . $_GET['to'] . chr(10) ;
                            echo 'FROM: ' . ((empty($From)) ? ((!empty($Object->responseData->detectedSourceLanguage)) ? 
                                $Object->responseData->detectedSourceLanguage : 'Auto') : $From) . chr(10) ;
                            echo 'TEXT: ' . $String . chr(10) ;
                            echo 'TRANSLATE: ' . $Object->responseData->translatedText . chr(10) ; 
                        }                        
                    }
                    else
                    {
                        echo 'ERROR: Supplied language ' . $From . ' is not a valid language.' . chr(10) ;
                    }
                }
                else
                {
                    echo 'ERROR: Supplied language ' . $_GET['to'] . ' is not a valid language.' .
                        chr(10) ;
                }                
            }
        break;
        case G_CALC:
            # Requires: &eq
            if (empty($_GET['eq']))
            {
                echo 'ERROR: Missing arguement &eq' . chr(10) ;
            }
            else
            {
                $Equation = utf8_encode(rawurlencode($_GET['eq'])) ;
                echo 'EQUATION: ' . $Equation . chr(10) ;
                $Obj = Google::calculate($Equation) ;
                if (!empty($Obj->error))
                {
                    switch ($Obj->error)
                    {
                        case '4':
                            echo 'ERROR: Syntax error. Bad equation.' . chr(10) ;
                        break ;
                        default:
                            echo 'ERROR: ' . $Obj->error . chr(10) ;
                        break ;
                    }
                }
                else
                {
                    echo 'PARSEDEQ: ' . $Obj->lhs . chr(10) ;
                    $Obj->rhs = str_replace(chr(160), ' ', $Obj->rhs) ;
                    $num = str_replace(' ', '', $Obj->rhs) ;
                    
                    #var_dump(preg_replace('\\x([\d\w]+)', null, $Obj->rhs)) ;                    
                    #var_dump($Obj) ;
                    echo 'ANSWER: ' . ((is_numeric($num)) ? str_replace(' ', ',', $Obj->rhs) : htmlentities(utf8_encode(rawurldecode($Obj->rhs)))) . chr(10) ;
                }
            }
        break;
        case G_CONVERT:
            # Requires: &to &from &amount
            if (empty($_GET['to']) || empty($_GET['amount']))
            {
                echo 'ERROR: Missing argument &to &amount' . chr(10) ;
            } 
            else
            {
                $Amount = (int)trim($_GET['amount']) ;
                $From = ((empty($_GET['from'])) ? 'USD' : $_GET['from']) ;
                $To = trim($_GET['to']) ;
                echo 'SEQUENCE: ' . sprintf('%d %s in %s', $Amount, $From, $To) . chr(10) ;
                $Convert = Google::convert($From, $To, $Amount) ;
                if (!empty($Convert->response))
                {
                    switch ($Convert->response)
                    {
                        case '4':
                            echo 'ERROR: Unknown components specified'. chr(10) ;
                        break ;
                        default:
                            echo 'PHP: ' . $Convert->response . chr(10) ;
                        break ;
                    }                     
                }
                if (!empty($Convert->error))
                {
                    if (stristr($Convert->error, 'Unit mismatch'))
                    {
                        echo 'ERROR: Unit mismatch' . chr(10) ;
                    }
                    else 
                    { 
                        switch ($Convert->error)
                        {
                            case '4':
                                echo 'ERROR: Unknown components specified'. chr(10) ;
                            break ;
                            default:
                                echo 'PHP: ' . $Convert->error . chr(10) ;
                            break ;
                        }  
                    }
                }
                else
                {
                    echo 'RESULT: ' . $Convert->answer . chr(10) ;
                    $Rate = (float)trim(substr($Convert->answer, 0, strpos($Convert->answer, ' '))) ;
                    $To = trim(substr($Convert->answer, strpos($Convert->answer, ' '))) ;
                    $From = trim(substr($Convert->equation, strpos($Convert->equation, ' '))) ;
                    echo 'RATE: ' . ($Rate / $Amount) . ' ' . $To . ' to ' . $From . chr(10) ;
                }
            }
        break;
        case G_MAP:
            # Requires: &start &end
            if (empty($_GET['start']) || empty($_GET['end']))
            {
                echo 'ERROR: Missing arguments &start and &end' . chr(10) ;
            }
            else
            {
                $Start = trim($_GET['start']) ;
                $End = trim($_GET['end']) ;
                $results = Google::maps($Start, $End) ;
                
                if (empty($results->routes))
                {
                    echo 'ERROR: Destination or start point not found' . chr(10) ;
                }
                else
                {
                    $results = $results->routes ;
                   	$_Start = str_replace(' ', '+', $results[0]->legs[0]->start_address); 
				    $_End = str_replace(' ', '+', $results[0]->legs[0]->end_address);
				    $Url = "http://maps.google.com/maps?f=d&source=s_d&saddr={$_Start}&daddr={$_End}&hl=en";
                    echo 'LINK: ' . ((SHORT_LINKS) ? Google::shortUrl($Url) : $Url) . chr(10) ;
                    echo 'START: ' . $results[0]->legs[0]->start_address . chr(10) ;
                    echo 'END: ' . $results[0]->legs[0]->end_address . chr(10) ;
                    echo 'DURATION: ' . $results[0]->legs[0]->duration->text . chr(10) ;
                    echo 'DISTANCE: ' . $results[0]->legs[0]->distance->text . chr(10) ;
                    
                    $results = $results[0]->legs[0]->steps ;
                    for ($i = 0; $i < count($results); $i++)
                    {
                        print $i + 1 . " " . strip_tags($results[$i]->html_instructions) . " ({$results[$i]->duration->text}/{$results[$i]->distance->text})\n" ;
                    }
                }
            }
        break;
        default:
            echo 'ERROR: Unsupported &switch.' . chr(10) ;
        break;
    }   
}
?>
