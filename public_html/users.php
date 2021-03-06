<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'OrgUser.php';
require_once 'DBTableList/Renderer/User.php';


// Users
$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
    header("Location: noright.php");
}

$list = new DBTableList(DSN, 10, 'user');
$list->setTable('organization_user');
$list->setColumns(array (
    'organization_user_id' => 'Id.'));
$list->orderby('organization_user_id');
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    $list->where('group_id='.$org_usr->getGroupId());
}
$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'users.html', 
    'contentmain', 
    'user',
    'user_entry',
    new UserColumnRenderer(),
    new UserRowRenderer($luConfig->getAdmin())
);
$list->accept($listrenderer);
$tpl->setVariable('new_user','<a href="user.php"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New User").'" /><br/>'._("New User").'</a>');
$tpl->setVariable('title', _("User Administration"));
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$tpl->show();

?>