<?php

include_once 'skin.inc';
require_once 'form/Form_Category.php';
require_once 'Categories.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'HttpParameter.php';

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
      header("Location: noright.php");
}    

$id   = HttpParameter::getParameter('org_id');
$org  = new HiringOrg($id);
$form = new Form_Category('cat','POST');

if ($form->validate()) {

}

$tpl->addBlockfile('contentmain','main', 'categories.html');
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);

#$renderer->setElementBlock(array('category'     => 'qf_group_table'));        
        

$form->accept($renderer);

$tpl->setVariable('org_name',$org->getValue('org_name'));

$tpl->touchBlock('main');
$tpl->setVariable('title',_("Categories"));
$tpl->show();

?>