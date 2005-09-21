<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'Key.php';
require_once 'Validate.php';
require_once 'HttpParameter.php';

$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
      header("Location: noright.php");
}    

$group=new OrgGroup($org_usr->getGroupId());

$form = new HTML_QuickForm('export','POST');

if (checkRights(HRJOBS_RIGHT_SYSTEM)){

    $form->registerRule('uri', 'callback', 'uri', 'Validate');
    $form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
    $form->addElement('header','test',_("Contact"));
    $form->addElement('text','wsdl', _("WSDL URL"),
            array('maxlength'=>'255',
                  'size'=>'80',
                  'class'=>'formFieldLong'));
    $form->addElement('checkbox','active', _("activate"));
    

    $form->addRule('wsdl',     _("URL is invalid"), 'uri');
    $form->setDefaults(array(
        'wsdl'   => $settings['ohrwurm']['wsdl'],
        'active' => $settings['ohrwurm']['active'])
                       );
                       
    $tpl->setVariable('export_title',_('OHRwurm connection'));
} 
if ($org_usr->getValue('is_group_admin')) {
    
    $form->registerRule('checkKey', 'callback', 'checkKey', 'Key');
    $form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> "._("Required")."</font>");
    $form->addElement('header','test',_("Contact"));

    $form->addElement('text','key', _("key"),
            array('maxlength' => '32',
                  'size'      => '40',
                  'class'     => 'formFieldLong'));
    $form->addElement('hidden','group_id',$org_usr->getGroupId());
    $form->addElement('hidden','name','OHRwurm');
    $form->addElement('submit','run',_("Run Export"));
    $form->addRule('key',     _("key is invalid"), 'checkKey');
    

    $form->setDefaults(array('group_id'=>$org_usr->getGroupId(),
                             'name'=>'OHRwurm',
                             'key'=>$group->getValue('export_key')                             
                             ));

    
    $tpl->setVariable('export_title',_('Connect to OHRwurm'));  
    $tpl->setVariable('groupname',$group->getValue('group_name'));
}

$form->addElement('submit','request',_("Save"));

if ($form->validate()) {
	
	if ($form->exportValue('request') && !HTML_QuickForm::isError($form->exportValue('request'))) {
	    if ($form->exportValue('key') && !HTML_QuickForm::isError($form->exportValue('key'))){
	        $group->setValue('export_key',$form->exportValue('key') );
	        $group->save();
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
	    }
	} elseif ($form->exportValue('run') && !HTML_QuickForm::isError($form->exportValue('run'))) {
		require_once('JobExporter.php');
		$exporter = new JobExporter($group);
		$exporter->run();
	}
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