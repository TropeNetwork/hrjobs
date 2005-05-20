<?php
/**
 * wrapper class for database connections
 *
 * @copyright  Copyright 2003
 * @author     Carsten Bleek <carsten@bleek.de>
 * @package    OpenHR
 * @version    $Revision: 1.2 $
 */

require_once 'DB.php';

class Database extends DB {

    static function getConnection( $dsn , $config = NULL)
    {
        $db = db::connect( $dsn , $config );
        
        if(DB::isError($db)){
            self::log("Cannot connect to DB",PEAR_LOG_EMERG);
            exit;
        }else{
            $db->setFetchMode( DB_FETCHMODE_ASSOC );
            $db->setErrorHandling( PEAR_ERROR_CALLBACK, "DBerrorHandler");
        }
        return $db;
    }

    private function log($string,$level=PEAR_LOG_INFO)
    {
        //$GLOBALS['log']->log($string,$level);
        print $string;
    }

}

function DBerrorHandler($obj){
    global $log;
    print "<h1>Ein Datenbankfehler ist aufgetreten</h1>\n";
    if (error_reporting()>0){
        print '<pre>';
        print_r($obj);
        print '</pre>';
    }
    //$log->log($obj->getCode()." ".$obj->getMessage(),PEAR_LOG_EMERG);
    print $obj->getMessage();
    die();
}

?>