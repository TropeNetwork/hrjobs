<?php

include_once 'skin.inc';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/OrgUser.php';


$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!$org_usr->getValue('is_group_admin')) {
    header("Location: noright.php");
}
$tpl->addBlockfile('contentmain','main', 'configuration.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', _("Configuration"));
$tpl->show();

?>