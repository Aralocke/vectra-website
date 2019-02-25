<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', 1) ;
ini_set('log_errors', 0) ;
error_reporting(E_ALL) ;
ob_start() ;

#We want to catch the E_PARSE, E_COMPILE_ERROR and E_ERROR
$Res = register_shutdown_function('shutdown') ;

# Requiring the base files
require_once ('Definitions.php') ;
require_once (INCLUDE_DIR . 'Functions.php') ;
require_once (INCLUDE_DIR . 'Config.php') ;
require_once (CLASS_DIR . 'Google.Class.php') ;
require_once (CLASS_DIR . 'Database.Class.php') ;
require_once (CLASS_DIR . 'PorterStemmer.php') ;

# Return select PHP errors to the output display
$Errors = set_error_handler('errorHandler') ;

# No parser will work without this definition
define('IN_PARSERS', true) ;

###  Generation Time ##
$Start = explode(' ', microtime()) ;
define('START', $Start[1] + $Start[0]) ;

# Objects
$Dbc = new SQLi_Driver() ;

$Dbc->sql_set($dbhost, $dbuser, $dbpass, $dbname, $dbport) ; 

# Parser Type
$Type = null ;
if (!empty($_GET['type']))
{
    $Type = $_GET['type'] ;
}

define ('SHORT_LINKS', ((isset($_GET['shortlinks']) && $_GET['shortlinks'] == '1') ? true : false)) ;

echo "<pre>\nSTART\n" ;
if (!empty($Type))
{
    $File = $Type . ".php" ;
    if (file_exists(PUBLIC_PARSER_DIR . $File))
    {
        require_once (PUBLIC_PARSER_DIR . $File) ;
    } 
    elseif (file_exists(PRIVATE_PARSER_DIR . $File))
    {
        require_once (PRIVATE_PARSER_DIR . $File) ;
    }
    else
    {
        echo "PHP: Could not locate the parser\n" ;
        printParserList() ;
    }
}
else
{
    printParserList() ;
}
# Get the Buffer in case we ever do something to it
$Buffer = ob_get_contents() ;

# End buffering
ob_end_clean() ;

# Output the buffer
echo $Buffer ;

###  Generation Time ##
$Stop = explode(' ', microtime()) ;
$Stop = $Stop[1] + $Stop[0] ;
$Time = round($Stop - START, 6) ;
if (isset($_GET['debug']))
{
    echo 'GENERATION: ' . $Time . 'secs' . chr(10) ;
}
#######################
echo "END\r\n\n" ;
unset($Dbc) ;

# Shutdown function. Enables catching of Certain pre-processed errors
# E_PARSE, E_COMPILE_ERROR and E_ERROR
function shutdown()
{
    if ($error = error_get_last())
    {
        if (isset($error['type']) && ($error['type'] == E_ERROR || $error['type'] ==
            E_PARSE || $error['type'] == E_COMPILE_ERROR))
        {
            ob_end_clean() ;

            if (!headers_sent())
            {
                header('HTTP/1.1 500 Internal Server Error') ;
            }

            echo '<pre>' . chr(10) . 'START' . chr(10) . 'PHP: Error in ' . $error['file'] . ' on line ' . $error['line'] .
                ' reason: ' . $error['message'] . chr(10) . 'END' . chr(13) . chr(10) . chr(10) ;
        }
    }
}

?>