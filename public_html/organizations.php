<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'OrgUser.php';

$org_usr = new OrgUser($usr->getProperty('authUserId'));

$list = new DBTableList(DSN, 10,'org');
$list->setTable('organization');
$list->setColumns(array ('org_id'   => _("Id"),
                         'org_name' => _("Name"),
                         'website'  => _("Website")));
$list->orderby('org_name');
if (!checkRights(HRADMIN_RIGHT_SYSTEM)) {
    $list->where('organization_group_id='.$org_usr->getGroupId());
}
$listrenderer = new DBTableList_Renderer_Sigma(& $tpl, 'organizations.html','contentmain','org');
$list->accept($listrenderer);
$tpl->setVariable('title', _("Organizations"));
$tpl->show();

?>