<?php
class Youtube 
{
   
   static function searchVideo ($Search)
   {
        $Domain = 'http://gdata.youtube.com' ;
		$Uri = '/feeds/api/videos?q=' . urlencode($Search) . '&max-results=10&v=2&alt=json' ;
        
        $Source = self::getData($Domain, $Uri) ;
        
        if (is_object($Source))
        {
            return $Source ;
        }
        
        $Source = json_decode($Source, true) ;
        
        if (!is_array($Source))
        {
            return null ;
        }
        #print_r($Source) ;
        $Source = $Source['feed'] ;
        if (empty($Source['entry']))
        {
            return array() ;
        }
        $Source = $Source['entry'] ;
        $Results = array() ;
        foreach ($Source as $Key => $Result)
        {
            $Obj = new stdClass ;
            #############################
            # Find the ID
            $Obj->id = explode(':', $Result['id']['$t']) ;
            $Obj->id = end($Obj->id) ;
            #############################
            
            #############################
            # Find the Title
            if (empty($Result['title']['$t']))
            {
                continue ;
            }
            $Obj->title = $Result['title']['$t'] ;
            #############################
            
            #############################
            # Find the Duration
            $Obj->duration = ((!empty($Result['media$group']['yt$duration']['seconds'])) ? $Result['media$group']['yt$duration']['seconds'] : 0) ;
            #############################
            
            #############################
            # Find the Rating
            if (empty($Result['gd$rating']))
            {
                $Obj->rating = array (
                   'average' => 0,
                   'max' => 0,
                   'min' => 0,
                   'numRaters' => 0
                ) ;
            }
            else
            {
                $Obj->rating = $Result['gd$rating'] ;
            }            
            #############################
            
            #############################
            # Find the Statistics
            $Obj->stats = $Result['yt$statistics'] ;
            #############################
            
            #############################
            # Find the Author
            $Obj->author = $Result['author'] ;
            if (!empty($Obj->author[0]))
            {
                $Obj->author = array(
                    'name' => $Obj->author[0]['name']['$t'],
                    'link' => $Obj->author[0]['uri']['$t']
                ) ;
            }
            #############################
            
            #############################
            # Find the Publish Time
            $Obj->published   = strtotime($Result['published']['$t'], time()) ;
            $Obj->lastUpdated = strtotime($Result['updated']['$t'], time()) ;
            #############################
            
            #############################
            # Find the Categories & Categories
            $Obj->categories = array() ;
            $Obj->keywords   = array() ;
            if (!empty($Result['category']))
            {
                for ($i = 1; $i < count($Result['category']); $i++)
                {
                    if (!empty($Result['category'][$i]['label']))
                    {
                        $Obj->categories[] = $Result['category'][$i]['term'] ;
                    }
                    else
                    {
                        $Obj->keywords[] = $Result['category'][$i]['term'] ;
                    }
                }
            }
            #############################
            $Results[] = $Obj ;
        }
        return $Results ;
   }
   
   static function searchUser ($Search)
   {
        $Domain = 'http://gdata.youtube.com' ;
		$Uri = '/feeds/api/users/' . urlencode($Search) . '?alt=json' ;
        
        $Source = self::getData($Domain, $Uri) ;
        
        if (is_object($Source))
        {
            return $Source ;
        }
        
        $Source = json_decode($Source, true) ;
        
        if (!is_array($Source))
        {
            return null ;
        }
        
        $Obj = new stdClass ;
        $Source = $Source['entry'] ;
        
        #############################
        # Find the Name
        if (empty($Source['yt$username']))
        {
            return null ;
        }        
        $Obj->name = $Source['yt$username']['$t'] ;
        #############################
        
        #############################
        # Find the Publish Time
        $Obj->joined = strtotime($Source['published']['$t']) ;
        $Obj->lastSeen = strtotime($Source['updated']['$t']) ;
        #############################
        
        #############################
        # Find the Categories & Categories
        $Obj->categories = array() ;
        if (!empty($Source['category']))
        {
            for ($i = 1; $i < count($Source['category']); $i++)
            {
                $Obj->categories[] = $Source['category'][$i]['term'] ;
            }
        } 
        #############################  
        
        #############################
        # Find the Link
        $Obj->link = 'http://youtube.com' ;
        if (!empty($Source['link'][0]))
        {
            $Obj->link = $Source['link'][0]['href'] ;
        }
        #############################
        
        #############################
        # Find the First name
        $Obj->firstName = ((!empty($Source['yt$firstName']['$t'])) ? $Source['yt$firstName']['$t'] : 'None Set') ;
        #############################
        
        #############################
        # Find the Location
        $Obj->location = $Source['yt$location']['$t'] ;
        #############################
        
        #############################
        # Find the Channel Stats
        $Obj->statistics = $Source['yt$statistics'] ;       
        $Obj->favourites = $Source['gd$feedLink'][0]['countHint'] ;
        $Obj->contacts   = $Source['gd$feedLink'][1]['countHint'] ;
        $Obj->uploads    = $Source['gd$feedLink'][5]['countHint'] ;
        #############################
        return $Obj ;
   }
   
   private static function assoc2numeric ($Array)
   {
        $Result = array() ;
        
        if (!is_array($Array))
        {
            return array() ;
        }
        
        foreach ($Array as $Key => $Data)
        {
            $Result[] = $Data ;
        }
        return $Result ;
   }
   
   private static function getData ($Domain, $Uri)
   {
        $IpList = array(
            '69.147.235.195',
            '69.147.235.196',
            '69.147.235.197'
        ) ;
        $Ch = curl_init($Domain . $Uri) ;
        if (!$Ch)
        {
            return false ;
        }
        
        curl_setopt($Ch, CURLOPT_INTERFACE, $IpList[array_rand($IpList)]) ;
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