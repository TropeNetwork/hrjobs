<?php

require_once 'Config.php';
$conf = new Config;
$root =& $conf->parseConfig(dirname(__FILE__).'/../config/config.xml', 'XML');
if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}
$settings = $root->toArray();
$settings = $settings['root']['conf'];
$initialized = $settings['setup']['initialized'];
if ($initialized!=1) {
    header("Location: setup.php");
    exit;
}
define('DSN','mysql://'.
             $settings['database']['user'].':'.
             $settings['database']['pass'].'@'.
             $settings['database']['host'].'/'.
             $settings['database']['name']);
define('THEME_BASE',$settings['theme']['base']);
define('THEME_SKIN',$settings['theme']['skin']);
define('HTML_BASE',$settings['html']['base']);
define('TEMPLATE_DIR',dirname(__FILE__).'/'.THEME_BASE.'/'.THEME_SKIN.'/');
define('IMAGES_DIR',THEME_BASE.'/'.THEME_SKIN.'/images');

?>