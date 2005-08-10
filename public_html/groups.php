<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'OrgUser.php';
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    header("Location: noright.php");
}
class GroupColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        if ($name=='disabled') {
            if (!$column) {
                return '<img src="'.IMAGES_DIR.'/active.png" alt="'._("Enabled").'" />';
            }else{
                return '<img src="'.IMAGES_DIR.'/inactive.png" alt="'._("Disabled").'" />';
            }
        }
        return $column;
    }
}
class GroupRowRenderer implements DBTableList_Renderer_Sigma_RowRenderer {
    public function renderRow(& $tpl,$row) {
        
    }
}
$org_usr = new OrgUser($usr->getProperty('authUserId'));
$list = new DBTableList(DSN, 10,'group');
$list->setTable('organization_group');
$list->setColumns(array (
    'group_id'      => 'Id.',
    'group_name'    => 'Name',
    'disabled'      => 'Aktiv'));
$list->orderby('group_name');
$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'groups.html', 
    'contentmain', 
    'group',
    'group_entry',
    new GroupColumnRenderer(),
    new GroupRowRenderer()
);
$list->accept($listrenderer);
$tpl->setVariable('new_group','<a href="group.php"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Group").'" /><br/>'._("New Group").'</a>');
$tpl->setVariable('title', _("Group Administration"));
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$tpl->show();

?>