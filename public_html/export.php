<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'GroupKeys.php';
require_once 'Key.php';
require_once 'Validate.php';
require_once 'HttpParameter.php';

$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
      header("Location: noright.php");
}    

$id   = HttpParameter::getParameter('org_id');
$org  = new HiringOrg($id);
$keys = new GroupKeys($org_usr->getGroupId());

$form = new HTML_QuickForm('export','POST');

if (empty($id) && checkRights(HRJOBS_RIGHT_SYSTEM)){

    $form->registerRule('uri', 'callback', 'uri', 'Validate');
    $form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
    $form->addElement('header','test',_("Contact"));
    $form->addElement('text','wsdl', _("WSDL URL"),
            array('maxlength'=>'255',
                  'size'=>'80',
                  'class'=>'formFieldLong'));
    $form->addElement('checkbox','active', _("activate"));
    $form->addElement('submit','request',_("Save"));

    $form->addRule('wsdl',     _("URL is invalid"), 'uri');
    $form->setDefaults(array(
        'wsdl'   => $settings['ohrwurm']['wsdl'],
        'active' => $settings['ohrwurm']['active'])
                       );
                       
    $tpl->setVariable('export_title',_('OHRwurm connection'));
} elseif (empty($id) && $org_usr->getValue('is_group_admin')) {
    
    $form->registerRule('checkKey', 'callback', 'checkKey', 'Key');
    $form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
    $form->addElement('header','test',_("Contact"));

    $form->addElement('text','key', _("key"),
            array('maxlength' => '32',
                  'size'      => '32',
                  'class'     => 'formFieldLong'));
    $form->addElement('hidden','group_id',$org_usr->getGroupId());
    $form->addElement('hidden','name','OHRwurm');
    $form->addElement('submit','request',_("Save"));
    $form->addRule('key',     _("key is invalid"), 'checkKey');
    $group_key=new GroupKeys($org_usr->getGroupId());

    $form->setDefaults(array('group_id'=>$org_usr->getGroupId(),
                             'name'=>'OHRwurm',
                             'key'=>$group_key->getKey('OHRwurm')->getValue('value')                             
                             ));

    $group=new OrgGroup($org_usr->getGroupId());
    $tpl->setVariable('export_title',_('Connect to OHRwurm'));  
    $tpl->setVariable('groupname',$group->getValue('group_name'));
} elseif ($id>0) {

    $form->registerRule('checkKey', 'callback', 'checkKey', 'Key');
    $form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
    $form->addElement('header','test',_("Contact"));

    $form->addElement('checkbox','activate_org', _("activate"));
    $form->addElement('hidden','org_id',$id);
    $form->addElement('hidden','name','OHRwurm');
    $form->addElement('submit','request',_("Save"));
    
    $group_key=new GroupKeys($org_usr->getGroupId());

    $form->setDefaults(array('org_id'=>$id,
                             'name'=>'OHRwurm',
                             'activate_org'=>$group_key->getKey('OHRwurm')->getOrganization($id)
                             ));

    $group=new OrgGroup($org_usr->getGroupId());
    $tpl->setVariable('export_title',$org->getValue('org_name'));  
}


if ($form->validate()) {
    if ($form->exportValue('key') && !HTML_QuickForm::isError($form->exportValue('key'))){
        Key::insert($form->exportValue('group_id'),
                        $form->exportValue('name'),
                        $form->exportValue('key'));
    }
    if ($form->exportValue('wsdl') && !HTML_QuickForm::isError($form->exportValue('wsdl'))){
        require_once('Config.php');
        $config = new Config;
        $root =& $config->parseConfig(_HRJOBS_CONFIG_FILE, 'XML');
        $settings = $root->toArray();
        $settings = $settings['root']['conf'];
        $settings['ohrwurm']['wsdl']   = $form->exportValue('wsdl');
        $settings['ohrwurm']['active'] = $form->exportValue('active');
        $config = new Config;
        $root =& $config->parseConfig($settings, 'phparray');
        $res = $config->writeConfig( _HRJOBS_CONFIG_FILE, 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage());
        } else {
        }                                                               
    } elseif ($form->exportValue('org_id') && !HTML_QuickForm::isError($form->exportValue('org_id'))){
        $keys = new GroupKeys($org_usr->getGroupId());
        $keys->getKey($form->exportValue('name'))->setOrganization($form->exportValue('org_id'),
                                                                   $form->exportValue('activate_org'));
                                                                   
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