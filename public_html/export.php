<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'OrgUser.php';

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
    header("Location: noright.php");
}    

$form = new HTML_QuickForm('export','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
$form->addElement('header','test',_("Contact"));

$form->addElement('text','key', _("key"),
            array('maxlength'=>'30',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('submit','request',_("Save"));
$form->addRule('email',     _("key is required"), 'required');
                                                

if ($form->validate()) {
  # 
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$form->accept($renderer);


$tpl->addBlockfile('contentmain','main', 'export.html');
$tpl->touchBlock('main');
$tpl->setVariable('title',_("Jobs Export"));
$tpl->show();

?>