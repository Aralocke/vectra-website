<?php

class Google
{

    const API_KEY = '' ;

    static function shortLang($lang)
    {

        $shortLang = array('en', 'de', 'ar', 'bg', 'ca', 'zh-CN', 'zh-TW', 'hr', 'cs',
            'da', 'nl', 'tl', 'fi', 'fr', 'el', 'iw', 'hi', 'id', 'it', 'ja', 'ko', 'lv',
            'lt', 'no', 'pl', 'pt', 'ro', 'ru', 'sr', 'sk', 'es', 'sl', 'sv', 'uk', 'vi',
            'af', 'sq', 'am', 'hy', 'az', 'eu', 'be', 'bn', 'bh', 'my', 'chr', 'zn', 'dv',
            'eo', 'et', 'gl', 'ka', 'gn', 'gu', 'is', 'iu', 'ga', 'kn', 'kk', 'km', 'ku',
            'ky', 'lo', 'mk', 'ms', 'ml', 'mt', 'mr', 'mn', 'ne', 'or', 'ps', 'ft', 'pa',
            'sa', 'sd', 'si', 'sw', 'tg', 'ta', 'tl', 'te', 'th', 'bo', 'tr', 'ur', 'uz',
            'ug', 'cy', 'yi', 'auto') ;

        return in_array($lang, $shortLang) ;
    }

    static function language($lang)
    {
        $languages = array('english' => 'en', 'german' => 'de', 'arabic' => 'ar',
            'bulgarian' => 'bg', 'catalan' => 'ca', 'chinese_simp' => 'zh-CN',
            'chinese_trad' => 'zh-TW', 'croatian' => 'hr', 'czech' => 'cs', 'danish' => 'da',
            'dutch' => 'nl', 'filipino' => 'tl', 'finnish' => 'fi', 'french' => 'fr',
            'greek' => 'el', 'hebrew' => 'iw', 'hindi' => 'hi', 'indonesian' => 'id',
            'italian' => 'it', 'japanese' => 'ja', 'korean' => 'ko', 'latvian' => 'lv',
            'lithuanian' => 'lt', 'norwegian' => 'no', 'polish' => 'pl', 'portuguese' =>
            'pt', 'romanian' => 'ro', 'russian' => 'ru', 'serbian' => 'sr', 'slovak' => 'sk',
            'spanish' => 'es', 'slovenian' => 'sl', 'swedish' => 'sv', 'ukrainian' => 'uk',
            'vietnamese' => 'vi', 'afrikaans' => 'af', 'albanian' => 'sq', 'amharic' => 'am',
            'armenian' => 'hy', 'azerbaijani' => 'az', 'basque' => 'eu', 'belarusian' =>
            'be', 'bengali' => 'bn', 'bihari' => 'bh', 'burmese' => 'my', 'cherokee' =>
            'chr', 'chinese' => 'zn', 'dhivehi' => 'dv', 'esperanto' => 'eo', 'estonian' =>
            'et', 'galician' => 'gl', 'georgian' => 'ka', 'guarani' => 'gn', 'gujarati' =>
            'gu', 'icelandic' => 'is', 'inuktitut' => 'iu', 'irish' => 'ga', 'kannada' =>
            'kn', 'kazakh' => 'kk', 'khmer' => 'km', 'kurdish' => 'ku', 'kyrgyz' => 'ky',
            'loathian' => 'lo', 'macedonian' => 'mk', 'malay' => 'ms', 'malayalam' => 'ml',
            'maltese' => 'mt', 'marathi' => 'mr', 'mongolian' => 'mn', 'nepali' => 'ne',
            'oriya' => 'or', 'pashto' => 'ps', 'persian' => 'ft', 'punjabi' => 'pa',
            'sanskrit' => 'sa', 'sindhi' => 'sd', 'sinhalese' => 'si', 'swahili' => 'sw',
            'tajik' => 'tg', 'tamil' => 'ta', 'tegalog' => 'tl', 'telugu' => 'te', 'thai' =>
            'th', 'tibetan' => 'bo', 'turkish' => 'tr', 'urdu' => 'ur', 'uzbek' => 'uz',
            'uighur' => 'ug', 'welsh' => 'cy', 'yiddish' => 'yi', 'detect' => 'auto') ;

        return array_key_exists($lang, $languages) ;
    }

    static function maps($Start, $End)
    {
        $url = 'http://maps.google.com/maps/api/directions/json?sensor=false&origin=' . urlencode($Start) . '&destination=' . urlencode($End) ;
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST']) ;
        $Data = curl_exec($ch) ;
        curl_close($ch) ;
        
        return json_decode($Data) ;
    }

    static function getLocationData($Url, $param)
    {
        $request_url = $Url . "&oe=utf-8&q=" . urlencode($param) ;
        $xml = simplexml_load_file($request_url) ;
        if (!empty($xml->Response))
        {
            $point = $xml->Response->Placemark->Point ;
            $Data = array() ;
            if (!empty($point))
            {
                $coordinatesSplit = split(",", $point->coordinates) ;
                // Format: Longitude, Latitude, Altitude
                $Data['latitude'] = $coordinatesSplit[1] ;
                $Data['longitude'] = $coordinatesSplit[0] ;
            }
            $Data['address'] = $xml->Response->Placemark->address ;
            $Data['country'] = $xml->Response->Placemark->AddressDetails->Country->
                CountryName ;
            $Data['countryCode'] = $xml->Response->Placemark->AddressDetails->Country->
                CountryNameCode ;
            $Data['areaName'] = $xml->Response->Placemark->AddressDetails->Country->
                AdministrativeArea->AdministrativeAreaName ;
            $Data['areaCode'] = $xml->Response->Placemark->AddressDetails->Country->
                AdministrativeArea ;
            if (!empty($administrativeArea->SubAdministrativeArea))
            {
                $Data['postalCode'] = $administrativeArea->SubAdministrativeArea->Locality->
                    PostalCode->PostalCodeNumber ;
            } elseif (!empty($administrativeArea->Locality))
            {
                $Data['postalCode'] = $administrativeArea->Locality->PostalCode->
                    PostalCodeNumber ;
            }
            return $Data ;
        }
        else
        {
            return false ;
        }
    }

    static function convert($from, $to, $amount)
    {
        $amount = urlencode(rawurldecode($amount)) ;
        $from = urlencode(rawurldecode($from)) ;
        $to = urlencode(rawurldecode($to)) ;        

        $url = 'http://www.google.com/ig/calculator?hl=en&q='.$amount.$from.'=?'.$to ;
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST']) ;
        $calc = curl_exec($ch) ;
        curl_close($ch) ;
        
        $Replace = array(
            'lhs',
            'rhs',
            'error',
            'icc'
        );
        $With = array(
            '"lhs"',
            '"rhs"',
            '"error"',
            '"icc"'
        );
        
        $Obj = json_decode(
            str_replace(
                $Replace,
                $With,
                $calc
            )
        ) ;
        
        $Object = new stdClass ;
        $Object->error = $Obj->error;
        $Object->equation = utf8_encode(rawurldecode($Obj->lhs));
        $Object->answer = utf8_encode(rawurldecode($Obj->rhs));
        $Object->response = utf8_encode(rawurldecode($Obj->icc));
   
        return $Object; 
    }
    
    static function calculate($equation)
    {
        $equation = urlencode(rawurldecode($equation)) ;
        $url = 'http://www.google.com/ig/calculator?hl=en&alt=json&q='.$equation ;
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST']) ;
        $calc = curl_exec($ch) ;
        curl_close($ch) ;
        
        $calc = substr(substr($calc, 1), 0, strlen($calc) - 2) ;
        $calc = explode(',', $calc) ;       
        $Object = new stdClass ;
        foreach ($calc as $result)
        {
            $result = explode(':', $result) ;
            $param = substr(substr(trim($result[1]), 1), 0, strlen(trim($result[1])) - 2) ;
            $Object->$result[0] = $param ;
        }
        return $Object; 
    }
    
    static function shortUrl($Url, $bool = true)
    {
        if (empty($Url))
        {
            return null ;
        }
        
        $Curl = curl_init(); 
		curl_setopt($Curl, CURLOPT_URL, 'http://goo.gl/api/url'); 
		curl_setopt($Curl, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($Curl, CURLOPT_POST, 1); 
		curl_setopt($Curl, CURLOPT_POSTFIELDS, 'user=toolbar@google.com&url='.urlencode($Url).'&auth_token='.Google::googleToken($Url)); 
		$Result = curl_exec($Curl); 
		curl_close($Curl);
		if ($Result)
        {
			$json = json_decode($Result);
            return ($bool && is_object($json)) ? $json->short_url : $json ;
		}
        return $Url ;
    }

    static function search($terms, $site = '')
    {
        $url = 'http://ajax.googleapis.com/ajax/services/search/web?v=1.0' ;
        $url .= '&q=' . ((!empty($site)) ? 'site:'.rawurlencode($site).'%20':'') . rawurlencode($terms) ;
        $url .= '&key=' . self::API_KEY . '&userip=' . $_SERVER['REMOTE_ADDR'] ;
        
        #echo $url . chr(10) ;
        
        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST']) ;
        $body = curl_exec($ch) ;
        curl_close($ch) ;
        $Object = json_decode($body) ;
        #var_dump($Object) ;
        return $Object ;
    }

    static function image($terms)
    {
        $url = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0' ;
        $url .= '&q=' . rawurlencode($terms) ;
        $url .= '&key=' . self::API_KEY . '&userip=' . $_SERVER['REMOTE_ADDR'] ;

        $ch = curl_init() ;
        curl_setopt($ch, CURLOPT_URL, $url) ;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_REFERER, 'http://' . $_SERVER['HTTP_HOST']) ;
        $body = curl_exec($ch) ;
        curl_close($ch) ;

        return json_decode($body) ;
    }

    /**
     * Translate a piece of text with the Google Translate API
     * @return String
     * @param $text String
     * @param $from String[optional] Original language of $text. An empty String will let google decide the language of origin
     * @param $to String[optional] Language to translate $text to
     */
    static function translate($text, $to = 'en', $from = '')
    {
        $url = 'http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=' .
             rawurlencode($text) . '&langpair=' . rawurlencode($from . '|' . $to) . '&userip=' . $_SERVER['REMOTE_ADDR'] ;
        $response = file_get_contents(
            $url, 
            null, 
            stream_context_create(
                array('http' =>
                    array(
                        'method' => "GET",
                        'header' => "Referer: http://" . $_SERVER['HTTP_HOST'] . "/\r\n"
                    )
                )
            )
        ) ;
        $response = json_decode($response) ;
        if (!empty($response->responseData->translatedText))
        {
            $response->responseData->translatedText = self::__unescapeUTF8EscapeSeq($response->responseData->translatedText) ;
        }
        return $response ;
    }

    /**
     * Convert UTF-8 Escape sequences in a string to UTF-8 Bytes. Old version.
     * @return UTF-8 String
     * @param $str String
     */
    private static function __unescapeUTF8EscapeSeq($str)
    {
        return preg_replace_callback("/\\\u([0-9a-f]{4})/i", create_function('$matches',
            'return html_entity_decode(\'&#x\'.$matches[1].\';\', ENT_NOQUOTES, \'UTF-8\');'),
            $str) ;
    }

    /**
     * Convert binary character code to UTF-8 byte sequence
     * @return String
     * @param $bin Mixed Interger or Hex code of character
     */
    private static function _bin2utf8($bin)
    {
        if ($bin <= 0x7F)
        {
            return chr($bin) ;
        } elseif ($bin >= 0x80 && $bin <= 0x7FF)
        {
            return pack("C*", 0xC0 | $bin >> 6, 0x80 | $bin & 0x3F) ;
        } elseif ($bin >= 0x800 && $bin <= 0xFFF)
        {
            return pack("C*", 0xE0 | $bin >> 11, 0x80 | $bin >> 6 & 0x3F, 0x80 | $bin & 0x3F) ;
        } elseif ($bin >= 0x10000 && $bin <= 0x10FFFF)
        {
            return pack("C*", 0xE0 | $bin >> 17, 0x80 | $bin >> 12 & 0x3F, 0x80 | $bin >> 6 &
                0x3F, 0x80 | $bin & 0x3F) ;
        }
    }
    
    /**
     * Functions used in the generation of the short url generator
     */
     private static function googleToken($b){
		$i = Google::token_tke($b);
		$i = $i >> 2 & 1073741823;
		$i = $i >> 4 & 67108800 | $i & 63;
		$i = $i >> 4 & 4193280 | $i & 1023;
		$i = $i >> 4 & 245760 | $i & 16383;
		$j = "7";
		$h = Google::token_tkf($b);
		$k = ($i >> 2 & 15) << 4 | $h & 15;
		$k |= ($i >> 6 & 15) << 12 | ($h >> 8 & 15) << 8;
		$k |= ($i >> 10 & 15) << 20 | ($h >> 16 & 15) << 16;
		$k |= ($i >> 14 & 15) << 28 | ($h >> 24 & 15) << 24;
		$j .= Google::token_tkd($k);
		return $j;
	}

	private static function token_tkc(){
		$l = 0;
		foreach(func_get_args() as $val){
			$val &= 4294967295;
			$val += $val > 2147483647 ? -4294967296 : ($val < -2147483647 ? 4294967296 : 0);
			$l   += $val;
			$l   += $l > 2147483647 ? -4294967296 : ($l < -2147483647 ? 4294967296 : 0);
		}
		return $l;
	}

	private static function token_tkd($l){
		$l = $l > 0 ? $l : $l + 4294967296;
		$m = "$l";
		$o = 0;
		$n = false;
		for($p = strlen($m) - 1; $p >= 0; --$p){
			$q = $m[$p];
			if($n){
				$q *= 2;
				$o += floor($q / 10) + $q % 10;
			} else {
				$o += $q;
			}
			$n = !$n;
		}
		$m = $o % 10;
		$o = 0;
		if($m != 0){
			$o = 10 - $m;
			if(strlen($l) % 2 == 1){
				if ($o % 2 == 1){
					$o += 9;
				}
				$o /= 2;
			}
		}
		return "$o$l";
	}

	private static function token_tke($l){
		$m = 5381;
		for($o = 0; $o < strlen($l); $o++){
			$m = Google::token_tkc($m << 5, $m, ord($l[$o]));
		}
		return $m;
	}

	private static function token_tkf($l){
		$m = 0;
		for($o = 0; $o < strlen($l); $o++){
			$m = Google::token_tkc(ord($l[$o]), $m << 6, $m << 16, -$m);
		}
		return $m;
	}
}

?>
