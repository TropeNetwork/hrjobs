<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'OrgUser.php';


$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
    header("Location: noright.php");
}
    
$tpl->addBlockfile('contentmain','main', 'configuration.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', _("Configuration"));
$tpl->show();

?>