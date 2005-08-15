<?php

include_once 'skin.inc';
require_once 'form/Form_Categories.php';
require_once 'Categories.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'HttpParameter.php';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
      header("Location: noright.php");
}    

$id   = HttpParameter::getParameter('org_id');
$org  = new HiringOrg($id);
$form = new Form_Categories('cat','POST');

if ($form->validate()) {

}

$tpl->addBlockfile('contentmain','main', 'categories.html');
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);

$form->accept($renderer);

$tpl->touchBlock('main');

// Profession
$list = new DBTableList(DSN, 10, 'profession');
$list->setTable('profession');
$list->setColumns(array (
    'profession_id' => 'Id.',
    'name'			=> 'Name',
));
$list->orderby('profession_id');
$list->where('group_id='.$org_usr->getGroupId());

$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'professions.html', 
    'profession_table', 
    'profession',
    'profession_entry'
);
$list->accept($listrenderer);
$tpl->touchBlock('profession');

// Industry
$list = new DBTableList(DSN, 10, 'industry');
$list->setTable('industry');
$list->setColumns(array (
    'industry_id' 	=> 'Id.',
    'name'			=> 'Name',
));
$list->orderby('industry_id');
$list->where('group_id='.$org_usr->getGroupId());

$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'industries.html', 
    'industry_table', 
    'industry',
    'industry_entry'
);
$list->accept($listrenderer);

// Location
$list = new DBTableList(DSN, 10, 'location');
$list->setTable('location');
$list->setColumns(array (
    'location_id' 	=> 'Id.',
    'name'			=> 'Name',
    'location_type'	=> 'Type',    
));
$list->orderby('location_id');
$list->where('group_id='.$org_usr->getGroupId());

$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'locations.html', 
    'location_table', 
    'location',
    'location_entry'
);
$list->accept($listrenderer);


$tpl->setVariable('org_name',$org->getValue('org_name'));

//$tpl->touchBlock('main');
$tpl->setVariable('new_profession','<a href="category.php?cat_type=2"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Profession").'" /><br/>'._("New Profession").'</a>');
$tpl->setVariable('new_industry','<a href="category.php?cat_type=1"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Industry").'" /><br/>'._("New Industry").'</a>');
$tpl->setVariable('new_location','<a href="category.php?cat_type=3"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Location").'" /><br/>'._("New Location").'</a>');

$tpl->setVariable('title',_("Categories"));
$tpl->show();

?>