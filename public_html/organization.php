<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'class/Date.php';
require_once 'class/HiringOrg.php';
require_once 'class/PostalAddress.php';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/HttpParameter.php';
require_once 'class/Categories.php';
require_once 'class/OrgUser.php';
$id = HttpParameter::getParameter('id');
$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (isset($id) && !$org_usr->hasRightOnOrganization($id) 
  && !checkRights(HRADMIN_RIGHT_SYSTEM)) {
    header("Location: noright.php");
    exit;
}
$tpl->setVariable('title',"Organisation");

$org = new HiringOrg($id);

if (isset($id)) {
    $address = $org->getAddress();    
} else {
    $address = new PostalAddress();
}

$form = new HTML_QuickForm('edit','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");
$form->addElement('header','test',_("Kontaktaufnahme"));
$form->addElement('text','org_name', _("Name"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','website', _("Website"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));      
$form->addElement('textarea','org_description', _("Beschreibung"),
            array('rows'=>'10',
                  'cols'=>'70',
                  'wrap'=>'on',
                  'class'=>'formFieldTextArea'));

$form->addElement('text','address', _("Adresse"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','street', _("Strasse"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','building_number', _("Hausnummer"),
            array('maxlength'=>'10',
                  'size'=>'3',
                  'class'=>'formFieldLong'));  
$form->addElement('text','postal_code', _("PLZ"),
            array('maxlength'=>'10',
                  'size'=>'5',
                  'class'=>'formFieldLong'));  
$form->addElement('text','region', _("Stadt"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','country_code', _("Land"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong'));  
$file =& $form->addElement('file', 'logo', _("Logo"));


$form->addElement('select','industry', _("Branche"),null, 
           array("size"=>"5",
                 "style"=>"width:200px;",
                 "multiple"=>"multiple") );
$form->addElement('hidden','industry_list');

$form->addElement('submit','save',_("Speichern"));
if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
} 
$defaults = array(
    'org_name'          => $org->getValue('org_name'),
    'website'           => $org->getValue('website'),
    'org_description'   => $org->getValue('org_description'),
    'address'           => $address->getValue('address'),
    'street'            => $address->getValue('street'),
    'building_number'   => $address->getValue('building_number'),
    'region'            => $address->getValue('region'),
    'postal_code'       => $address->getValue('postal_code'),
    'country_code'      => $address->getValue('country_code'),
);
$form->setDefaults($defaults);
$form->addRule('org_name',          "Bitte geben Sie den \"Name\" ein", 'required', null,'server');
$form->addRule('street',            "Bitte geben Sie die \"Straße\" ein", 'required', null,'server');
$form->addRule('building_number',   "Bitte geben Sie die \"Hausnummer\" ein", 'required', null,'server');
$form->addRule('region',            "Bitte geben Sie die \"Stadt\" ein", 'required', null,'server');
$form->addRule('postal_code',       "Bitte geben Sie die \"PLZ\" ein", 'required', null,'server');
$form->addRule('country_code',      "Bitte geben Sie das \"Land\" ein", 'required', null,'server');
$form->addRule('website',           "Bitte geben Sie die \"Website\" ein", 'required', null,'server');


if ($form->validate()) {    
    $org->setValue('org_name',$form->exportValue('org_name'));
    $org->setValue('website',$form->exportValue('website'));
    $org->setValue('organization_group_id',$org_usr->getGroupId());
    $org->setValue('org_description',$form->exportValue('org_description'));
    $list = $form->exportValue('industry_list');
    $cat = split(",",$list);
    sort($cat);
    $org->setIndustries(array_unique($cat));
    
    $org->save();
    if ($file->isUploadedFile()) {
        $path = $_SERVER['DOCUMENT_ROOT'].'/logos/'.$org->getValue('org_id');
        if (!file_exists($path)) {
            mkdir($path,0777);
        }
        $filename = 'logo.gif';
        
        $file->moveUploadedFile($path, $filename);
        $org->setValue('logo_file_name',$filename);
        $org->save();
    }
    $address->setValue('organization_org_id',$org->getValue('org_id'));
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
$category = Categories::getCategoryValues($org->getIndustries(),Categories::TYPE_INDUSTRY);
$script = '
       <script type="text/javascript">
       <!--
';     
foreach ($category as $key => $value) {       
    $script .= "addValue('$key','$value','industry');\n";
}              
$script .= '
        -->
     </script>'; 
$tpl->setVariable('industry_script',$script);
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
    'given_name'    => 'Vorname',
    'family_name'   => 'Name',
    'email'         => 'Email'
));
$clist->orderby('family_name');
if (!isset($id)) {
    $id=0;
}
$clist->where('organization_org_id='.$id);
$clistrenderer = new DBTableList_Renderer_Sigma(& $tpl, 'contacts.html', 'contacts', 'contact');
$clist->accept($clistrenderer);

//$tpl->setVariable('title',"Organisation");
$tpl->setVariable('id',$id);
$tpl->setVariable('new_contact','<a title="Neuen Kontakt anlegen" href="contact.php?id='.$id.'"><img src="'.IMAGES_DIR.'/new.png" alt="Neuer Kontakt" /></a><br/><br/>');
$tpl->show();

?>