<?php

error_reporting(E_ALL);
require_once 'hradmin.config.inc';
require_once 'Var_Dump.php';
require_once 'HTML/Template/Sigma.php';


$lu_dsn = array('dsn' => $dsn);

define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['group']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['group']['admins']);

if (!$usr->isLoggedIn()) {
    
    $tpl->loadTemplateFile('login.html');
    $tpl->setVariable('base',HTML_BASE);
    $tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
    
    $tpl->show();
    exit;
} 

$login  = 'Benutzer: '.$usr->getProperty('handle').'<br> ';
$login .= '<a href="?logout=1">Abmelden</a>';

$tpl->setVariable('login_top',$login);
$level = 0;

function checkRights($right) {
    global $level;
    global $delete;
    global $usr;
    $level = $usr->checkRight($right);

    if (!$level) {
        return false;
    }
    if ($level < 3) {
        $delete = false;   
    }
    return $level;
}

?>