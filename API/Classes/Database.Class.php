<?php

class sqliDriverException extends Exception
{
}
class SQLi_Driver
{

    const DB_HOST = '' ;
    const DB_USER = '' ;
    const DB_PASS = '' ;
    const DB_NAME = '' ;
    const DB_PORT =  3306;

    const MYSQL_OBJECT = 4 ;
    
    private $socket ;
    
    # Runtime vars
    private $mysqli_connect_error ;    
    private $mysqli_error ;
    private $mysqli_errno ;
    private $last_result ;
    
    ####
    # DB Vars
    ####
    private $dbhost ;
    private $dbuser ;
    private $dbpass ;
    private $dbname ;
    private $dbport ;
    
    static function sql_getInstance($DbHost, $DbUser, $DbPasswd, $DbName, $DbPort)
    {
        return mysqli_connect($DbHost, $DbUser, $DbPasswd, $DbName, $DbPort) ;
    }

    function __construct()
    {
        $this->socket      = null ;
        $this->last_result = null ;
        
        $this->dbhost   = self::DB_HOST ;
        $this->dbuser   = self::DB_USER ;
        $this->dbpass   = self::DB_PASS ;
        $this->dbname   = self::DB_NAME ;
        $this->dbport   = self::DB_PORT ;
        
        $this->mysqli_connect_error = '' ;
        $this->mysqli_error = '' ;
        $this->mysqli_errno = 0 ;
        return ;
    }

    function __destruct()
    {
        $this->sql_close() ;
        return ;
    }
    
    function setDbprop($Prop, $toSet)
    {
        $this->{ucwords($Rrop)} = $toSet ;
        return ;
    }

    function isActive()
    {
        $Ping = @mysqli_ping($this->socket) ;
        $this->mysqli_error = @mysqli_error($this->socket) ;
        $this->mysqli_errno = @mysqli_errno($this->socket) ;
        return $Ping ;
    }
    
    function sql_set ($DbHost = self::DB_HOST, $DbUser = self::DB_USER, $DbPasswd = self::DB_PASS, 
        $DbName = self::DB_NAME, $DbPort = self::DB_PORT)
    {
        $this->dbhost = $DbHost ;
        $this->dbuser = $DbUser ;
        $this->dbpass = $DbPasswd ;
        $this->dbname = $DbName ;
        $this->dbport = $DbPort ;
        
        return ;
    }
    
    function connect () 
    {
        if (is_object($this->socket))
        {
            $this->sql_close() ;
        }
    
        if (empty($this->dbhost) || empty($this->dbpass) || empty($this->dbuser) || empty($this->dbname))
        {
            return false ;
        }
      
        return $this->sql_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport) ;
    }

    function sql_connect($DbHost = self::DB_HOST, $DbUser = self::DB_USER, $DbPasswd = self::DB_PASS, 
        $DbName = self::DB_NAME, $DbPort = self::DB_PORT)
    {
        if (is_object($this->socket))
        {
            $this->sql_close() ;
        }

        $this->socket = self::sql_getInstance($DbHost, $DbUser, $DbPasswd, $DbName, $DbPort) ;
        if (is_object($this->socket))
        {
            $this->dbhost = $DbHost ;
            $this->dbuser = $DbUser ;
            $this->dbpass = $DbPasswd ;
            $this->dbname = $DbName ;
            $this->dbport = $DbPort ;
            
            return true ;
        }
     
        $this->socket = null ;
        $this->mysqli_connect_error = @mysqli_connect_error() ;
        return false ;
    }

    function sql_close()
    {
        if (!is_object($this->socket))
        {
            return false ;
        }
        return @mysqli_close($this->socket) ;
    }

    function sql_affected_rows()
    {
        if (!is_object($this->socket))
        {
            return 0 ;
        }

        return @mysqli_affected_rows($this->socket) ;
    }

    function sql_query($Query)
    {
        if (!is_object($this->socket))
        {
            return false ;
        }

        if (empty($Query))
        {
            return false ;
        }
        
        if (@mysqli_ping($this->socket) == false)
        {
            $this->sql_connect($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport) ;
        }
        
        $this->sql_clear_error() ;
        
        $Query = str_replace(array("\r", "\n", "\t"), '', $Query);
        if (($this->last_result = @mysqli_query($this->socket, $Query)) === false)
        {
            $this->sql_error($this->last_result) ;
        }       
        
        return $this->last_result ;
    }

    function sql_fetch(&$Resource, $Type = self::MYSQL_OBJECT)
    {
        if (!is_object($this->socket))
        {
            return null ;
        }

        if (is_bool($Resource))
        {
            return null ;
        }               

        switch ($Type)
        {
            case MYSQL_ASSOC:
                return @mysqli_fetch_assoc($Resource) ;
                break ;
            case MYSQL_NUM:
                return @mysqli_fetch_array($Resource, MYSQL_NUM) ;
                break ;
            case MYSQL_BOTH:
                return @mysqli_fetch_assoc($Resource, MYSQL_BOTH) ;
                break ;
            default:
                return @mysqli_fetch_object($Resource) ;
                break ;
        }
    }

    function sql_info()
    {
        if (!is_object($this->socket))
        {
            return null ;
        }

        return @mysqli_info($this->socket) ;
    }

    function sql_num_rows(&$Resource)
    {
        if (!is_object($this->socket))
        {
            return 0 ;
        }

        if (is_bool($this->socket))
        {
            return 0 ;
        }

        return @mysqli_num_rows($Resource) ;
    }

    function sql_escape($String)
    {
        if (!is_object($this->socket))
        {
            return $String ;
        }

        if (get_magic_quotes_gpc())
        {
            $String = stripslashes($String) ;
        }

        return @mysqli_real_escape_string($this->socket, $String) ;
    }
    
    function sql_clear_error()
    {
        if (!is_object($this->socket))
        {
            return $String ;
        }
        
        $this->mysqli_error = '' ;
        $this->mysqli_errno = '' ;
        return ;
    }

function sql_get_connect_error()
{
    if (!is_object($this->socket))
        return '' ;

    return $this->mysqli_connect_error;
}

    function sql_error($Type = 4)
    {
        if (is_null($this->socket))
        {
            return $this->mysqli_connect_error ;
        }
        
        if ($Type === false)
        {
            $this->mysqli_error = @mysqli_error($this->socket) ;
            $this->mysqli_errno = @mysqli_errno($this->socket) ;
            return ;
        }
        
        if ($Type === true)
        {
            return empty($this->mysqli_error) ;
        }

        Switch ($Type)
        {
            case 1:
                return $this->mysqli_error ;
            break ;
            case 2:
                return $this->mysqli_errno ;
            break ;
            case 3:
                return $this->socket->sqlstate ;
            break ;
            case 4:
                $Obj = array() ;
                $Obj['error'] = $this->mysqli_error ;
                $Obj['errno'] = $this->mysqli_errno ;
                $Obj['state'] = $this->socket->sqlstate ;        
                return $Obj ;
            break ;
            default:
                $Obj = new stdClass ;
                $Obj->error = $this->mysqli_error ;
                $Obj->errno = $this->mysqli_errno ;
                $Obj->state = $this->socket->sqlstate ;        
                return $Obj ;
            break ;
        }
    }

    function sql_freeresult(&$Resource)
    {
        if (!is_object($this->socket))
        {
            return false ;
        }

        if (!is_resource($Resource))
        {
            return false ;
        }

        if (is_bool($Resource))
        {
            return false ;
        }
        
        return @mysqli_free_result($Resource) ;
    }

    function sql_get_num_rows($Query)
    {
        if (!is_object($this->socket))
        {
            return false ;
        }

        $Resource = $this->sql_query($Query) ;

        if (is_bool($Resource))
        {
            return 0 ;
        }

        $Results = $this->sql_num_rows($Resource) ;
        $this->sql_freeresult($Resource) ;
        return $Results ;
    }
    
    function sql_rows_matched()
    {
        if (!is_object($this->socket))
        {
            return 0 ;
        }
        
        $error = $this->sql_error(1) ;
        if (is_null($error))
        {
            return 0 ;
        }
        
        preg_match('#Matched: (\d+)#i', $error, $Match) ;
        return (!empty($Match)) ? (int)$Match[1] : 0 ;
    }
}

?>