<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'JobPosition.php';
require_once 'OrgUser.php';
require_once 'Contact.php';
require_once 'Date.php';
require_once 'HttpParameter.php';

$id = HttpParameter::getParameter('id');
$cid = HttpParameter::getParameter('cid');

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (isset($id) && !$org_usr->hasRightOnOrganization($id)  && !$org_usr->getValue('is_group_admin')) {
    header("Location: noright.php");
    exit;
}
if (isset($id)) {
    $org = new HiringOrg($id);
} else {
    $org = $org_usr->getOrganization();
}

$form = new HTML_QuickForm('post','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");
$form->addElement('text','given_name', _("First Name"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
                  
$form->addElement('text','family_name', _("Family Name"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','email', _("Email"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','phone_areacode', _("Phone"),
            array('maxlength'=>'6',
                  'size'=>'3',
                  'class'=>'formFieldLong'));
$form->addElement('text','phone_number', _("Phone"),
            array('maxlength'=>'20',
                  'size'=>'10',
                  'class'=>'formFieldLong'));
$form->addElement('text','phone_extention', _("Phone"),
            array('maxlength'=>'5',
                  'size'=>'2',
                  'class'=>'formFieldLong'));
$form->addElement('text','fax_areacode', _("Fax"),
            array('maxlength'=>'6',
                  'size'=>'3',
                  'class'=>'formFieldLong'));
$form->addElement('text','fax_number', _("Fax"),
            array('maxlength'=>'20',
                  'size'=>'10',
                  'class'=>'formFieldLong'));
$form->addElement('text','fax_extention', _("Fax"),
            array('maxlength'=>'5',
                  'size'=>'2',
                  'class'=>'formFieldLong'));
$form->addElement('submit','save',_("Save"));
$form->addElement('submit','delete',_("Delete"));



if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
}
if (isset($cid)) {
    $form->addElement('hidden', 'cid', $cid);
    $contact = new Contact($cid);
    $defaults = array(
        'given_name'        => $contact->getValue('given_name'),
        'family_name'       => $contact->getValue('family_name'),
        'email'             => $contact->getValue('email'),
        'phone_areacode'    => $contact->getValue('phone_areacode'),
        'phone_number'      => $contact->getValue('phone_number'),
        'phone_extention'   => $contact->getValue('phone_extention'),
        'fax_areacode'      => $contact->getValue('fax_areacode'),
        'fax_number'        => $contact->getValue('fax_number'),
        'fax_extention'     => $contact->getValue('fax_extention'),
    );    
    $form->setDefaults($defaults);
}
$form->addRule('family_name',       _("Please enter the \"Name\" "), 'required', null,'server');
$form->addRule('email',             _("Please enter the \"Email\" "), 'required', null,'server');
$form->addRule('email',             _("Please enter a valid \"Email\" "), 'email', null,'server');
$form->addRule('phone_areacode',    _("Must be a number"), 'numeric', null,'server');
$form->addRule('phone_number',      _("Must be a number"), 'numeric', null,'server');
$form->addRule('phone_extention',   _("Must be a number"), 'numeric', null,'server');
$form->addRule('fax_areacode',      _("Must be a number"), 'numeric', null,'server');
$form->addRule('fax_number',        _("Must be a number"), 'numeric', null,'server');
$form->addRule('fax_extention',     _("Must be a number"), 'numeric', null,'server');

if ($form->validate()) {
    if (isset($cid)) {  
        $contact->setValue('contact_id',$cid);
    }else{
        $contact = new Contact();
    }
    if ($form->exportValue('save')) {
        $contact->setValue('given_name',        $form->exportValue('given_name'));
        $contact->setValue('family_name',       $form->exportValue('family_name'));
        $contact->setValue('email',             $form->exportValue('email'));
        $contact->setValue('phone_areacode',    $form->exportValue('phone_areacode'));
        $contact->setValue('phone_number',      $form->exportValue('phone_number'));
        $contact->setValue('phone_extention',   $form->exportValue('phone_extention'));
        $contact->setValue('fax_areacode',      $form->exportValue('fax_areacode'));
        $contact->setValue('fax_number',        $form->exportValue('fax_number'));
        $contact->setValue('fax_extention',     $form->exportValue('fax_extention'));
        $contact->setValue('org_id',$id);
        $contact->save();
    } elseif ($form->exportValue('delete')) {
        $contact->delete();
    }
    header("Location: organization.php?id=".$id);
    exit;
} 
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$tpl->addBlockfile('contentmain','form', 'contact.html');
$form->accept($renderer);


$tpl->setVariable('title',_("Contact"));
if ($org!=null) {
    $tpl->setVariable('org_name',$org->getValue('org_name'));
}

$tpl->show();


?>