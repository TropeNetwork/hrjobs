<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'OrgUser.php';
require_once 'OrgKeys.php';
require_once 'HttpParameter.php';


$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
      header("Location: noright.php");
}    

$id   = HttpParameter::getParameter('org_id');
$org  = new HiringOrg($id);
$keys = new OrgKeys($id);

$form = new HTML_QuickForm('export','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
$form->addElement('header','test',_("Contact"));

$form->addElement('text','key', _("key"),
            array('maxlength'=>'32',
                  'size'=>'32',
                  'class'=>'formFieldLong'));
$form->addElement('hidden','org_id',$id);
$form->addElement('hidden','name','OHRwurm');
$form->addElement('submit','request',_("Save"));
$form->addRule('email',     _("key is required"), 'required');
                                                

if ($form->validate()) {
    $client = new SoapClient("http://ohrwurm.neon/soap/index/OHRwurmIndex.wsdl",array('trace' =>1));
    try {
        if ($client->checkKey($form->exportValue('key'))) {
            Key::insert($form->exportValue('org_id'),
                        $form->exportValue('name'),
                        $form->exportValue('key'));
        }
    } catch (SoapFault $fault) {
        trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
    }
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$form->accept($renderer);

$tpl->setVariable('org_name',$org->getValue('org_name'));
$tpl->addBlockfile('contentmain','main', 'export.html');
$tpl->touchBlock('main');
$tpl->setVariable('title',_("Jobs Export"));
$tpl->show();

?>