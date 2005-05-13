<?php

error_reporting(E_ALL);
require_once 'hradmin.config.inc';
require_once 'Var_Dump.php';
require_once 'HTML/Template/Sigma.php';

$lu_dsn = array('dsn' => $dsn);

define('HRADMIN_APP',$hradmin_settings['application']);
define('HRADMIN_AREA',$hradmin_settings['area']);
//define('HRADMIN_GROUP_USERS',$hradmin_settings['group']['users']);
//define('HRADMIN_GROUP_ADMINS',$hradmin_settings['group']['admins']);

// define the right constants
$admin->perm->outputRightsConstants('constant',array(
    'prefix'       =>'HRJOBS_RIGHT',
    'area'         => HRADMIN_AREA,
    'application'  => HRADMIN_APP
),'php');
//print_r($usr);
if (!$usr->isLoggedIn()) {
    
    $tpl->loadTemplateFile('login.html');
    if ($usr->getStatus()==-9) {
        $tpl->setVariable('error_message',_('Login failed'));
    }
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