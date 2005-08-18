<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'OrgUser.php';

class OrgColumnRenderer implements DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column) {
        if ($name=='enable_export') {
        	if ($column==0) {
        		return '';
        	}
            return "<img src=\"{theme}/images/multisynk_16x16.png\" alt=\""._("Export")."\"/>";
        }
        return $column;
    }
}

$org_usr = new OrgUser($usr->getProperty('auth_user_id'));

$list = new DBTableList(DSN, 10,'org');
$list->setTable('organization left join organization_group on organization.group_id=organization_group.group_id');
$list->setColumns(array(
	'organization.org_id AS org_id' => _("Id"),
    'group_name'                    => _("Group"),
    'org_name'                      => _("Name"),
    'website'                       => _("Website"),
    'enable_export'					=> _("Enable Export"),
));
$list->orderby('org_name');
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    $list->where('organization.group_id='.$org_usr->getGroupId());
}
$listrenderer = new DBTableList_Renderer_Sigma(
	& $tpl, 
	'organizations.html',
	'contentmain',
	'org',
	'org_entry',
	new OrgColumnRenderer()
);
$list->accept($listrenderer);
$tpl->setVariable('title', _("Organizations"));
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$tpl->show();

?>