<?php
final class Simple_Youtube_User
    extends stdClass
{
    public $id = null ;
    public $name = null ;
    public $joined = null ;
    public $lastSeen = null ;
    public $category = null ;
    public $title = null ;
    public $content = null ;
    public $firstName = 'Not Set' ;
    public $age = null;
    public $gender = null;
    public $location = 'Not Set' ;
    public $subscribers = null ;
    public $views = null ;
    public $favorites = null ;
    public $contacts = null ;
    public $uploads = null ;
}
final class Simple_Youtube_Video
    extends stdClass
{
    public $title = null ;
    public $link = null ;
    public $author = null ;
    public $authorLink = null ;
    public $views = 0 ;
    public $length = 0 ;
    public $favorites = 0 ;
    public $categories = array() ;
    public $rating = array ('average' => 0, 'max' => 0, 'min' => 0, 'numRaters' => 0) ;
    public $stats = array ('likes' => 0, 'dislikes' => 0) ;
    public $id = null ;
    public $published = 0 ;
    public $updated = 0 ;
    public $keywords = array() ;
}
class Youtube_Parser
{
    
    const DEVELOPER_KEY = 'AI39si4i3zSaSwJadj9zjzywpC_QIQzk2L0QHToWcX8jq7g5sT7zbD2xSebLmgPjBEn7F4Yrkoq_f_pDhQjuqCDTofP0lGLoow' ;
    const VIDEO_SEARCH = 1 ;
    const USER_SEARCH = 2 ;
    const GDATA_SERVER = 'http://gdata.youtube.com' ;
    
    public $doc ;
    public $errors = array() ;
    
    function searchVideo ($query)
    {
        $Uri = '/feeds/api/videos?q=' . urlencode($query) . '&max-results=10&v=2&key='.self::DEVELOPER_KEY ;
        return self::getXMLdata($Uri) ;
    } 
    
    function searchUser ($query)
    {
        $Uri = '/feeds/api/users/' . urlencode($query) .'?key='.self::DEVELOPER_KEY ;
        return self::getXMLdata($Uri) ;
    }
    
    function getXMLdata ($query)
    {
        $this->doc = self::getData( self::GDATA_SERVER , $query ) ;
        return $this->doc ;
    }
    
    function parseXML ($doc)
    {
        if (is_object($doc))
            return $doc ;
        $obj = @simplexml_load_string($doc) ;  
        return $obj ;
    }
    
    static function parseXMLfeed ($doc, $type = 1)
    {
        switch ($type)
        {
            case 1:
                return self::parseVideoSearch($doc) ;
            break ;
            case 2:
                return self::parseUserSearch($doc) ;
            break ;
            default:
                return null ;
            break ;
        }
        
        return null ;
    }

    private static function parseUserSearch ($doc)
    {
        $obj = new Simple_Youtube_User ;

        if (empty($doc))
        {
            return null ;
        } 
        
        if (empty($doc->id))
        {
            return null ;
        }
        
        $obj->id = (string) $doc->id ;
        
        $obj->name = (string) $doc->author->name ;
        
        if (!empty($doc->published)) {
            $obj->joined = strtotime((string) $doc->published, time()) ;
        }
        
        if (!empty($doc->updated)) { 
            $obj->lastSeen = strtotime((string) $doc->updated,time()) ;
        }
        
          if (sizeof($doc->category) > 0) { 
            $attrs = $doc->category[1]->attributes() ;
            $obj->category = (string) $attrs['term'] ;
          }
          
          if (!empty($doc->title)) {
            $obj->title = (string) $doc->title ;
          }
          
          if (!empty($doc->content)) {
            $obj->content = (string) str_replace("\n"," ",$doc->content) ;
          }
          
          $yt = $doc->children('http://gdata.youtube.com/schemas/2007') ;
          
          if (!empty($yt->firstName)) {
            $obj->firstName = (string) $yt->firstName ;
          }
          
          if (!empty($yt->age)) {
            $obj->age = (string) $yt->age ;
          }
          
           if (!empty($yt->gender)) {
            $obj->gender = str_replace(array('m','f'),array('male','female'),(string) $yt->gender) ;
          }
          
          if (!empty($yt->location)) {
            $obj->location = (string) $yt->location ;
          }   
               
            if ($yt->statistics)
            {
                $attrs = $yt->statistics->attributes() ;           
                $obj->subscribers = (int) $attrs['subscriberCount'] ; 
                $obj->views = (int) $attrs['viewCount'] ; 
            }
            
            $gd = $doc->children('http://schemas.google.com/g/2005') ;
            $array = array('favorites','contacts',null,null,null,'uploads',null);
            
            for ($a = 0; $a < sizeof($gd->feedLink); $a++) {
                if ($array[$a] !== null) {
                    $attrs = $gd->feedLink[$a]->attributes() ;
                    $obj->$array[$a] = (int) $attrs['countHint'] ;
             }
        }
          return $obj;    
    }
    
    private static function parseVideoSearch ($doc)
    {
        $data = array() ;

        if (empty($doc->entry))
        {
            return $data ;
        }       
        
        $results = $doc->entry ;
        
        for ($i = 0; $i < sizeof($results) ; $i++)
        {
            $obj = new Simple_Youtube_Video ;
            $video = $results[$i] ;
            // Parse XML video Object -->
            if (!empty($video->title))
                $obj->title = (string) $video->title ;
            // Do not proceed if title is null    
            if (empty($obj->title))
                continue ;
                
            if (!empty($video->published))
                $obj->published = strtotime((string) $video->published, time()) ;
                
            if (!empty($video->updated))
                $obj->updated = strtotime((string) $video->updated, time()) ;
            
            if (!empty($video->id))
            {
                $id = explode(':', (string) $video->id) ;
                $obj->id = $id[sizeof($id) - 1] ;
                $obj->link = 'http://www.youtube.com/watch?v=' . $obj->id ;
            }
                
            if (sizeof($video->category) > 0)                
                for ($n = 1; $n < sizeof($video->category); $n++)
                {
                    $attrs = $video->category[$n]->attributes() ;
                    if (isset($attrs['label']))
                        $obj->categories[] = (string) $attrs['label'] ;
                    if (stristr((string) $attrs['scheme'], 'keywords'))
                        $obj->keywords[] = (string) $attrs['term'] ;
                } 
           
            if (!empty($video->author))
                $obj->author = (string) $video->author->name ;
            
            if (!empty($video->author))
                $obj->authorLink = (string) $video->author->uri ;
            
            // get <yt:duration> node for video length
            $media = $video->children('http://search.yahoo.com/mrss/');              
            $yt = $media->children('http://gdata.youtube.com/schemas/2007');  
            if ($yt->duration)
            {
                $attrs = $yt->duration->attributes();
                $obj->length = (int) $attrs['seconds'] ;
            } 
            
            // get <yt:stats> node for viewer statistics
            $yt = $video->children('http://gdata.youtube.com/schemas/2007');            
            if ($yt->statistics)
            {
                $attrs = $yt->statistics->attributes() ;            
                $obj->views = (int) $attrs['viewCount'] ; 
                $obj->favorites = (int) $attrs['favoriteCount'];
            }
            
            if ($yt->rating)
            {
                $attrs = $yt->rating->attributes() ;
                $obj->stats = array (
                    'likes' => (int) $attrs['numLikes'],
                    'dislikes' => (int) $attrs['numDislikes']
                ) ;
            }
            
            // get <gd:rating> node for video ratings
            $gd = $video->children('http://schemas.google.com/g/2005'); 
            if ($gd->rating) {
                $attrs = $gd->rating->attributes();
                $obj->rating = array (
                    'average' => (int) $attrs['average'],
                    'max' => (int) $attrs['max'],
                    'min' => (int) $attrs['min'],
                    'numRaters' => (int) $attrs['numRaters']
                ) ;
            }
              
            // END XML Parse -->
            $data[$i] = $obj ;
        }
        
        return $data ;
    }
    
    private static function getData ($Domain, $Uri)
    {
        $Ch = curl_init($Domain . $Uri) ;
        if (!$Ch)
        {
            return false ;
        }
        
        curl_setopt($Ch, CURLOPT_INTERFACE, '69.147.235.196') ;
        curl_setopt($Ch, CURLOPT_TIMEOUT, 20) ;
        curl_setopt($Ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($Ch, CURLOPT_FOLLOWLOCATION, true) ;
        curl_setopt($Ch, CURLOPT_FRESH_CONNECT, 1) ;
        curl_setopt($Ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 ( .NET CLR 3.5.30729; .NET4.0E)') ;
        
        $File = curl_exec($Ch) ;
        if (!curl_errno($Ch))
        {
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
    }
    
}
?>