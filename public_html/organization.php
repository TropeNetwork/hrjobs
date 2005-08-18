<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'Date.php';
require_once 'HiringOrg.php';
require_once 'PostalAddress.php';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'form/Form_Organization.php';
require_once 'Database.php';
require_once 'HttpParameter.php';
require_once 'Categories.php';
require_once 'OrgUser.php';

$id = HttpParameter::getParameter('id');
$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (isset($id) && !$org_usr->hasRightOnOrganization($id) 
  && !checkRights(HRJOBS_RIGHT_SYSTEM)) {
    header("Location: noright.php");
    exit;
}
$tpl->setVariable('title',_("Organization"));

$org = new HiringOrg($id);

if (isset($id)) {
    $address = $org->getAddress();    
} else {
    $address = new PostalAddress();
}

$group_id = $org_usr->getGroupId();

$form = new Form_Organization('edit','POST');

if (!isset($group_id) || $group_id===0) {
    if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
        header("Location: noright.php");
        exit;
    }
    $select_group = getGroups();
    $form->addElement('select','group', _("Gruppe"),$select_group);
    $form->addRule('group',          _("Please select a \"Group\" "), 'required', null,'server');
}

if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
}
$industry = $form->getElement('industry');
$industry->loadQuery(Database::getConnection(DSN),'SELECT industry_id, name from industry where group_id='.$group_id.';','name','industry_id');
$industry->setSelected(array_keys($org->getIndustries()));

$defaults = array(
    'group'             => $org->getValue('group_id'),
    'org_name'          => $org->getValue('org_name'),
    'website'           => $org->getValue('website'),
    'org_description'   => $org->getValue('org_description'),
    'address'           => $address->getValue('address'),
    'street'            => $address->getValue('street'),
    'building_number'   => $address->getValue('building_number'),
    'region'            => $address->getValue('region'),
    'postal_code'       => $address->getValue('postal_code'),
    'country_code'      => $address->getValue('country_code'),
    'enable_export'		=> $org->getValue('enable_export'),
);
$form->setDefaults($defaults);


if ($form->validate()) {    
    $org->setValue('org_name',$form->exportValue('org_name'));
    $org->setValue('website',$form->exportValue('website'));
    $org->setValue('group_id',$org_usr->getGroupId());
    $org->setValue('org_description',$form->exportValue('org_description'));
    $org->setValue('enable_export',$form->exportValue('enable_export'));
    $group_id = $form->getSubmitValue("group");
    if (isset($group_id)) {
        $org->setValue('group_id',$group_id);
    }
    $org->setIndustries($form->exportValue('industry'));
    
    $org->save();
    $file = $form->getFile();
    if ($file->isUploadedFile()) {
        
        $path = realpath('logos').'/'.$org->getValue('org_id');
        if (!file_exists($path)) {
            mkdir($path,0777);
        }
        $filename = 'logo.gif';
        
        $file->moveUploadedFile($path, $filename);
        $org->setValue('logo_file_name',$filename);
        $org->save();
    }
    $address->setValue('org_id',$org->getValue('org_id'));
    $address->setValue('address',$form->exportValue('address'));
    $address->setValue('street',$form->exportValue('street'));
    $address->setValue('building_number',$form->exportValue('building_number'));
    $address->setValue('region',$form->exportValue('region'));
    $address->setValue('postal_code',$form->exportValue('postal_code'));
    $address->setValue('country_code',$form->exportValue('country_code'));
    $address->save();
    
    //header('Location: organizations.php');
}
if ($org->getValue('logo_file_name')) {
    $tpl->setVariable('logo','<img title="'.$org->getValue('org_name').'" src="/logos/'.$org->getValue('org_id').'/'.$org->getValue('logo_file_name').'" alt="logo">');
}


$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('');
$tpl->addBlockfile('contentmain','org', 'organization.html');
$form->accept($renderer);

// Contacts
$clist = new DBTableList(DSN, 10, 'contact');
$clist->setTable('contact');
$clist->setColumns(array (
    'contact_id'    => '',
    'given_name'    => _("First Name"),
    'family_name'   => _("Family Name"),
    'email'         => _("Email")
));
$clist->orderby('family_name');
if (!isset($id)) {
    $id=0;
}
$clist->where('org_id='.$id);
$clistrenderer = new DBTableList_Renderer_Sigma(& $tpl, 'contacts.html', 'contacts', 'contact','contact_entry');
$clist->accept($clistrenderer);

$tpl->setVariable('id',$id);
$tpl->setVariable('new_contact','<a title="'._("New Contact").'" href="contact.php?id='.$id.'"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Contact").'" /></a><br/><br/>');
$tpl->show();

function getGroups() {
    $db = Database::getConnection(DSN);
    $query="SELECT group_id, group_name FROM organization_group ";
    $result = $db->query($query);
    $groups = array();
    while ($row = $result->fetchRow()) {
        $groups[$row['group_id']] = $row['group_name'];
    }
    return $groups;         
}

?>