<?php

error_reporting(E_ALL);

require_once 'Config.php';
$conf = new Config;

// define pathes, configuration files and make sure that library path is within the include_path
// use '_HRJOBS' prefix for all HRJobs related definitions.

define('_HRJOBS_BASE_DIR',    realpath(dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..'));
define('_HRJOBS_LIB_DIR',     _HRJOBS_BASE_DIR . DIRECTORY_SEPARATOR . 'lib');
define('_HRJOBS_CONFIG_DIR',  _HRJOBS_BASE_DIR . DIRECTORY_SEPARATOR . 'config');
define('_HRJOBS_CONFIG_FILE', _HRJOBS_CONFIG_DIR . DIRECTORY_SEPARATOR . 'config.xml' ); 

if (!ereg( _HRJOBS_LIB_DIR, ini_get('include_path'))){
  ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . _HRJOBS_LIB_DIR );
}

$root =& $conf->parseConfig( _HRJOBS_CONFIG_FILE, 'XML');
if (PEAR::isError($root)) {
    die('Error while reading configuration '. _HRJOBS_CONFIG_FILE .': '. $root->getMessage());
}

$settings = $root->toArray();
$settings = $settings['root']['conf'];
$initialized = $settings['setup']['initialized'];

define('DSN',$settings['database']['type'].'://'.
             $settings['database']['user'].':'.
             $settings['database']['pass'].'@'.
             $settings['database']['host'].'/'.
             $settings['database']['name']);


define('THEME_BASE',$settings['theme']['base']);
define('THEME_SKIN',$settings['theme']['skin']);
define('HTML_BASE',$settings['html']['base']);
define('TEMPLATE_DIR',dirname(__FILE__).'/'.THEME_BASE.'/'.THEME_SKIN.'/');
define('IMAGES_DIR',THEME_BASE.'/'.THEME_SKIN.'/images');
// i18n                   
define('LOCALE',$settings['i18n']['locale']);
define('CHARSET',$settings['i18n']['charset']);

if (!isset($settings['ohrwurm'])){
    $settings['ohrwurm']['wsdl']="http://ohrwurm.net/soap/index/OHRwurmIndex.wsdl";
    $settings['ohrwurm']['active']=false;
}

?>