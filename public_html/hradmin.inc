<?php
require_once 'hradmin.config.inc';
require_once 'LiveUser/Admin/Perm/Container/DB_Medium.php';
require_once 'LiveUser/Admin/Auth/Container/DB.php';
require_once 'LiveUser/Perm/Container/DB/Medium.php';
require_once 'HTML/Template/Sigma.php';


$lu_dsn = array('dsn' => $dsn);


$objRightsAdminAuth = new
    LiveUser_Admin_Auth_Container_DB(
        $lu_dsn, $conf['authContainers'][0]
    );


$objRightsAdminPerm = new
    LiveUser_Admin_Perm_Container_DB_Medium($conf['permContainer']);


if (!$objRightsAdminPerm->init_ok) {
    die('impossible to initialize' . $objRightsAdminPerm->getMessage());
}


define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['group']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['group']['admins']);

$objRightsAdminPerm->setCurrentLanguage('DE');
$objRightsAdminPerm->setCurrentApplication(HRADMIN_APP);
$objRightsAdminPerm->outputRightsConstants(array('prefix'       =>'HRADMIN_RIGHT',
                                                 'area'         => HRADMIN_AREA,
                                                 'application'  => HRADMIN_APP),'php');
                                                 
$objRightsAdminAuth->authTableCols= $conf['authContainers'][0]['authTableCols'];

if (!$usr->isLoggedIn()) {
    
    $tpl->loadTemplateFile('login.html');
    $tpl->setVariable('base',HTML_BASE);
    $tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
    
    $tpl->show();
    exit;
} 

$objRightsPerm = $usr->getPermContainer();
if (!$objRightsPerm->init_ok) {
    die('impossible to initialize' . $objRightsPerm->getMessage());
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