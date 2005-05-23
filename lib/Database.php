<?php
/**
 * wrapper class for database connections
 *
 * @copyright  Copyright 2003
 * @author     Carsten Bleek <carsten@bleek.de>
 * @package    OpenHR
 * @version    $Revision: 1.3 $
 */

require_once 'DB.php';
require_once 'Var_Dump.php';

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
        print "<style>\ntable {font-size:12px;}\ntd {padding:3px;empty-cells: show;border:1px dashed black}</style>\n";
        Var_Dump::displayInit(
            array(
                'display_mode' => 'HTML4_Table'
            ),
            array(
                'show_caption'   => false,
                'bordercolor'    => '#DDDDDD',
                'bordersize'     => '0',
                'captioncolor'   => 'white',
                'cellpadding'    => '0',
                'cellspacing'    => '0',
                'color1'         => '#FFFFFF',
                'color2'         => '#F4F4F4',
                'before_num_key' => '<font color="#CC5450"><b>',
                'after_num_key'  => '</b></font>',
                'before_str_key' => '<font color="#5450CC">',
                'after_str_key'  => '</font>',
                'before_value'   => '<i>',
                'after_value'    => '</i>',
                'start_table'    =>
                    '<table style="border-collapse: collapse;border:1px solid black" cellpadding="0" cellspacing="0" >' ,
                'end_table'      => '</td></tr></table>'."\n",
                'start_tr'       => '<tr valign="top" bgcolor="#F8F8F8">'."\n",
                'start_tr_alt'   => '<tr valign="top" bgcolor="#E8E8E8">'."\n",
            )
        );
        Var_dump::display($obj);        
    }
    //$log->log($obj->getCode()." ".$obj->getMessage(),PEAR_LOG_EMERG);
    //print $obj->getMessage();
    die();
}

?>