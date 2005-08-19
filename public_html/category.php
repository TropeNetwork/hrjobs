<?php

include_once 'skin.inc';
require_once 'form/Form_Category.php';
require_once 'Categories.php';
require_once 'Profession.php';
require_once 'Industry.php';
require_once 'Location.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'HttpParameter.php';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';

$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (!checkRights(HRJOBS_RIGHT_SYSTEM) && !$org_usr->getValue('is_group_admin')) {
	header("Location: noright.php");
}    

$cat_id   	= HttpParameter::getParameter('cat_id');
$cat_type	= HttpParameter::getParameter('cat_type');
$form 		= new Form_Category('cat','GET');


switch ($cat_type) {
	case Categories::SECTION_PROFESSION:
		$cat = new Profession($cat_id);
		$title = _("Profession");
		break;
	case Categories::SECTION_INDUSTRY:
		$cat = new Industry($cat_id);
		$title = _("Industry");
		break;
	case Categories::SECTION_LOCATION:
		$cat = new Location($cat_id);
		$title = _("Location");
		$form->addElement(
			'select', 
			'type', 
			_("Location Type"),
			array(
				'region'	=> _("region"),
				'country'	=> _("country"),
				'state'		=> _("state"),
				'city'		=> _("city"),
				'zip'		=> _("zip")
			)
		);
		$defaults['type'] = $cat->getValue('location_type');
		break;
	default:
		header('Location: categories.php');
		exit;
		break;
}

$form->addElement('hidden', 'cat_id', $cat_id);
$form->addElement('hidden', 'cat_type', $cat_type);

$defaults['name'] = $cat->getValue('name');

  
$form->setDefaults($defaults);

if ($form->validate()) {
	
	if ($form->exportValue('save')) {
		if ($form->exportValue('type') && !HTML_QuickForm::isError($form->exportValue('type'))) {
			$cat->setValue('location_type',$form->exportValue('type'));
		}
		$cat->setValue('group_id',$org_usr->getValue('group_id'));
		$cat->setValue('name',$form->exportValue('name'));
		$cat->save();
		header('Location: categories.php');
		exit;
	} else if ($form->exportValue('delete') 
		&& !HTML_QuickForm::isError($form->exportValue('delete'))) {
		if ($form->exportValue('cat_id') 
			&& !HTML_QuickForm::isError($form->exportValue('cat_id'))) {
			$cat->delete();
		}
		header('Location: categories.php');
		exit;
	}
	
}

$tpl->addBlockfile('contentmain','main', 'category.html');
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$form->accept($renderer);
$tpl->touchBlock('main');
$tpl->setVariable('title',$title);
$tpl->show();

?>