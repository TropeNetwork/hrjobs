<?php

include_once 'skin.inc';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/OrgUser.php';

$org_usr = new OrgUser($usr->getProperty('authUserId'));

$list = new DBTableList(DSN, 10,'org');
$list->setTable('organization');
$list->setColumns(array ('org_id'   => 'Organisations Id.',
                         'org_name' => 'Name',
                         'website'  => 'Website'));
$list->orderby('org_name');
if (!checkRights(HRADMIN_RIGHT_SYSTEM)) {
    $list->where('organization_group_id='.$org_usr->getGroupId());
}
$listrenderer = new DBTableList_Renderer_Sigma(& $tpl, 'organizations.html','contentmain','org');
$list->accept($listrenderer);
$tpl->setVariable('title', "Organisationen");
$tpl->show();

?>