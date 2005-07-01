<?php

error_reporting(E_ALL);
require_once 'LiveUserConfiguration.php';
require_once 'Var_Dump.php';
require_once 'HTML/Template/Sigma.php';


define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['groups']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['groups']['admins']);

$luConfig = new LiveUserConfiguration($settings);

$usr  = $luConfig->getLiveUser();

// define the right constants
$luConfig->getPermAdmin()->outputRightsConstants('constant',array(
    'prefix'       =>'HRJOBS_RIGHT',
    'area'         => HRADMIN_AREA,
    'application'  => HRADMIN_APP
),'php');

if (!$usr->isLoggedIn()) {
    $tpl->loadTemplateFile('login.html');
    if (isset($_REQUEST['handle'])) {
        $tpl->setVariable('error_message',_('Login failed'));
        $tpl->setVariable('error_message','<div class="error">'.$usr->statusMessage($usr->getStatus()).'<div>');
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