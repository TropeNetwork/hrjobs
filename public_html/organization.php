<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'Date.php';
require_once 'HiringOrg.php';
require_once 'PostalAddress.php';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
require_once 'HttpParameter.php';
require_once 'Categories.php';
require_once 'OrgUser.php';
$id = HttpParameter::getParameter('id');
$org_usr = new OrgUser($usr->getProperty('authUserId'));
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

$form = new HTML_QuickForm('edit','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");
$form->addElement('header','test',_("Contact"));
$form->addElement('text','org_name', _("Name"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('text','website', _("Website"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));      
$form->addElement('textarea','org_description', _("Description"),
            array('rows'=>'10',
                  'cols'=>'70',
                  'wrap'=>'on',
                  'class'=>'formFieldTextArea'));

$form->addElement('text','address', _("Address"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','street', _("Street"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','building_number', _("Building Number"),
            array('maxlength'=>'10',
                  'size'=>'3',
                  'class'=>'formFieldLong'));  
$form->addElement('text','postal_code', _("Zip"),
            array('maxlength'=>'10',
                  'size'=>'5',
                  'class'=>'formFieldLong'));  
$form->addElement('text','region', _("City"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
$form->addElement('text','country_code', _("Country"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong'));  
$file =& $form->addElement('file', 'logo', _("Logo"));


$form->addElement('select','industry', _("Industry"),null, 
           array("size"=>"5",
                 "style"=>"width:200px;",
                 "multiple"=>"multiple") );
$form->addElement('hidden','industry_list');

$form->addElement('submit','save',_("Save"));
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
$form->addRule('org_name',          _("Please enter a \"Name\" "), 'required', null,'server');
$form->addRule('street',            _("Please enter a \"Street\" "), 'required', null,'server');
$form->addRule('building_number',   _("Please enter a \"Building Number\" "), 'required', null,'server');
$form->addRule('region',            _("Please enter a \"City\" "), 'required', null,'server');
$form->addRule('postal_code',       _("Please enter a \"Zip\" "), 'required', null,'server');
$form->addRule('country_code',      _("Please enter a \"Country\" "), 'required', null,'server');
$form->addRule('website',           _("Please enter a \"Website\" "), 'required', null,'server');


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
    'given_name'    => _("First Name"),
    'family_name'   => _("Family Name"),
    'email'         => _("Email")
));
$clist->orderby('family_name');
if (!isset($id)) {
    $id=0;
}
$clist->where('organization_org_id='.$id);
$clistrenderer = new DBTableList_Renderer_Sigma(& $tpl, 'contacts.html', 'contacts', 'contact');
$clist->accept($clistrenderer);

$tpl->setVariable('id',$id);
$tpl->setVariable('new_contact','<a title="'._("New Contact").'" href="contact.php?id='.$id.'"><img src="'.IMAGES_DIR.'/new.png" alt="'._("New Contact").'" /></a><br/><br/>');
$tpl->show();

?>