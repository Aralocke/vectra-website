<?php
abstract class CacheSocket
{
    static protected $IpList = array (
    ) ;
    
    const CACHE_DIR = 'Cache/' ;    

    const AGENT = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)' ;
    const SSL   = false ;
    const CACHE = true ;
    
    static function get ($Target, $CurlOpts, $Interface = null, $Cache = self::CACHE, $SSL = self::SSL, $Agent = self::AGENT)
    {   
        if (!empty($Interface))
        {
            $IpList    = array_flip(self::$IpList) ;
            $Interface = $IpList[array_rand($IpList)] ;
        }
        $Ch = curl_init() ;
        if (!$Ch)
        {
            return false ;
        }
        curl_setopt($Ch, CURLOPT_URL, $Target) ;
        curl_setopt($Ch, CURLOPT_INTERFACE, $Interface) ;
        curl_setopt($Ch, CURLOPT_TIMEOUT, 20) ;
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true) ;
        curl_setopt($Ch, CURLOPT_USERAGENT, $Agent) ;
        if (!empty($CurlOpts))
        {
            @curl_setopt_array($Ch, $CurlOpts) ;
        }

        $File = curl_exec($Ch) ;
        if (!curl_errno($Ch))
        {
            $Info = curl_getinfo($Ch) ;
            print_r($Info) ;
            curl_close($Ch) ;
            return $File ;
        }
        else
        {
            $Obj = new stdClass ;
            $Obj->errno = curl_errno($Ch) ;
            $Obj->error = curl_error($Ch) ;
            curl_close($Ch) ;
            return $Obj ;
        }
        
        return null ;
    }
    
    static function post ($Target, $PostData = array(), $CurlOpts = array(), $Ssl = false, $UserAgent = null)
    {

    }
    
    static private function cacheFile ()
    {
        
    }
    
    static private function deleteCache($CacheFile)
    {
        $Filename = self::CACHE_DIR . substr(sha1($CacheFile), 0, 15) . '._cache' ;
        if (@file_exists($Filename))
        {
            if (unlink($Filename))
            {
                return true ;
            }
            return false ;
        }
        return false ;
    }
}
?>