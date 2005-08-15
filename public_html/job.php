<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'JobPosition.php';
require_once 'OrgUser.php';
require_once 'Date.php';
require_once 'HttpParameter.php';
require_once 'Categories.php';
require_once 'form/Form_Job.php';
require_once 'Database.php';

$id = HttpParameter::getParameter('id');

$org_usr = new OrgUser($usr->getProperty('auth_user_id'));
if (isset($id) && !$org_usr->hasRightOnJob($id) 
  && !checkRights(HRJOBS_RIGHT_SYSTEM)) {
    header("Location: noright.php");
    exit;
}
$group_id = $org_usr->getGroupId();
$org = $org_usr->getOrganizations();
$job = new JobPositionPosting($id);
$disabled = false;
if (isset($id)) {
    $disabled = ($job->getValue('job_status')!=JobPositionPosting::STATUS_ACTIVE);
}

$form = new Form_Job('edit','POST');
$form->addElement('header','test',_("Contact"));
$orgs = array();
if (empty($org)) {
    $tpl->setVariable('error',_("Create an organization first!"));
} else {
    foreach ($org as $orgid => $obj) {
        $orgs[$orgid] = $obj->getValue('org_name');
    }
}
$form->addElement('select','org_id', _("Organization"),$orgs);

$form->registerElementType('dyndate','DynDate.php','HTML_QuickForm_dyndate');
$dateelement = 'dyndate';
if ($disabled) {
    $dateelement = 'date';
}
$form->addElement($dateelement,'end_date', _("End date"),
    array('addEmptyOption'=>true,
          'emptyOptionValue' => '0',
          'language' => 'de',
          'minYear' => Date::minYear(),
          'maxYear' => Date::minYear()+2,
          'format' => Date::quickFormat()));
$form->addElement($dateelement, 'start_date', _("Start date"),
    array('language' => 'de',
          'minYear' => Date::minYear(),
          'maxYear' => Date::minYear()+2,
          'format' => Date::quickFormat()));
$form->addElement('select','stylesheet', _("Stylesheet"),getStylesheets());
if (!$disabled) {
    $form->addElement('submit','save',_("Save"));
    if (isset($id)) {
        if ($job->getValue('is_template')==0) {
            $form->addElement('submit','disable_job',_("Disable"));
        } else {
            $form->addElement('submit','post_job',_("Post"));
        }
    }
} else {
    $form->addElement('submit','save',_("Save as Template"));
    $form->addElement('submit','activate_job',_("Activate Job"));
    $form->freeze();
}
if (isset($id)) {
    $form->addElement('submit','delete',_("Delete"));
}


$contact_options = array('-1'=>'keine');
if ($job->getValue('org_id')) {
    $organization = new HiringOrg($job->getValue('org_id'));
    $contacts = $organization->getContacts();
    if (!empty($contacts)) {
    foreach ($contacts as $contact) {
        $contact_options[$contact->getValue('contact_id')] = 
            $contact->getValue('given_name').' '.$contact->getValue('family_name');
    }
    }
}
$contact_select = $form->addElement('select','apply_contact',_("Contact"));



if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
}
if (!$job->getValue('start_date')) {
    $start = date('Y-m-d'); 
} else {
    $start = $job->getValue('start_date');
}
if (!$job->getValue('end_date') 
        || $job->getValue('end_date')=='0000-00-00') {
    $end = '';
} else {
    $end = $job->getValue('end_date');
}


$cat = $form->getElement('profession');
$cat->loadQuery(Database::getConnection(DSN),'SELECT profession_id, name from profession where group_id='.$group_id.';','name','profession_id');
$cat->setSelected(array_keys($job->getProfessions()));

$cat = $form->getElement('location');
$cat->loadQuery(Database::getConnection(DSN),'SELECT location_id, name from location where group_id='.$group_id.';','name','location_id');
$cat->setSelected(array_keys($job->getLocations()));


$defaults = array(
    'job_title'             => $job->getValue('job_title'),
    'job_reference'         => $job->getValue('job_reference'),
    'org_id'   				=> $job->getValue('org_id'),
    'start_date'            => $start,
    'end_date'              => $end,
    'job_description'       => $job->getValue('job_description'),    
    'job_requirements'      => $job->getValue('job_requirements'),
    'apply_contact'         => $job->getValue('apply_contact_id'),
    'apply_by_email'        => $job->getValue('apply_by_email'),
    'apply_by_web'          => $job->getValue('apply_by_web'),
    'apply_by_web_url'      => $job->getValue('apply_by_web_url'),
    'stylesheet'            => $job->getValue('stylesheet'),    
);

$form->setDefaults($defaults);

$form->registerRule ('dates', 'callback', 'checkDates');
$form->addRule('job_title',             "Bitte geben Sie den \"Job Titel\" ein", 'required', null,'server');
$form->addRule('org_id',   "Bitte geben Sie eine \"Organisation\" ein", 'required', null,'server');
$form->addRule('apply_contact',         "Bitte wählen Sie einen \"Bewerberkontakt\" aus", 'required', null,'server');
$form->addRule(array('start_date', 'end_date'), 'Das Endedatum muß größer sein als das Startdatum', 'dates', null, 'server');
if ($form->validate()) {
    $job->setValue('job_title',             $form->exportValue('job_title'));
    $job->setValue('job_description',       $form->exportValue('job_description'));
    $job->setValue('job_requirements',      $form->exportValue('job_requirements'));
    $job->setValue('job_reference',         $form->exportValue('job_reference'));
    $job->setValue('start_date',            Date::sqlDate($form->exportValue('start_date')));
    $job->setValue('end_date',              Date::sqlDate($form->exportValue('end_date')));
    $job->setValue('org_id',   				$form->exportValue('org_id'));
    $job->setValue('apply_contact_id',      $form->exportValue('apply_contact'));
    $job->setValue('apply_by_email',        $form->exportValue('apply_by_email'));
    $job->setValue('apply_by_web',          $form->exportValue('apply_by_web'));
    $job->setValue('apply_by_web_url',      $form->exportValue('apply_by_web_url'));
    $job->setValue('stylesheet',            $form->exportValue('stylesheet'));
    if ($job->getValue('apply_contact_id')==='-1') {
    	$job->setValue('apply_contact_id',null);
    }
    
    $job->setProfessions($form->exportValue('profession'));
    $job->setLocations($form->exportValue('location'));
    if ($job->getValue('is_template')===null) {
        $job->setValue('is_template',false);
    }
    if ($form->exportValue('save')) {
        if ($job->getValue('job_status')==JobPositionPosting::STATUS_INACTIVE) {
            $job->setValue('is_template',true);
            $job->setValue('job_status',JobPositionPosting::STATUS_ACTIVE);
            $job->save();
            header("Location: jobs.php");
            exit;
        } else {
            $job->save();
            $form->addElement('hidden', 'id', $job->getValue('job_id'));
            $form->addElement('submit','delete',_("Delete"));
            $form->addElement('submit','disable_job',_("Disable"));
        }
    } elseif ($form->exportValue('delete')) {
        $job->delete();
        if ($job->getValue('is_template')==0) {
            header("Location: jobs.php");
        } else {
            header("Location: jobs.php?templates=1");
        }
        exit;
    } elseif ($form->exportValue('disable_job') && !HTML_QuickForm::isError($form->exportValue('disable_job'))) {
        $job->disable();
        header("Location: jobs.php");
        exit;
    } elseif ($form->exportValue('activate_job') && !HTML_QuickForm::isError($form->exportValue('activate_job'))) {
        $job->activate();
        header("Location: jobs.php");
        exit;
    } elseif ($form->exportValue('post_job') && !HTML_QuickForm::isError($form->exportValue('post_job'))) {
        $job->postTemplate();
        header("Location: jobs.php");
        exit;
    }
    
    $contact_options = array('-1'=>'keine');
    if ($job->getValue('org_id')) {
        $organization = new HiringOrg($job->getValue('org_id'));
        $contacts = $organization->getContacts();
        if (!empty($contacts)) {
        foreach ($contacts as $contact) {
            $contact_options[$contact->getValue('contact_id')] = 
                $contact->getValue('given_name').' '.$contact->getValue('family_name');
        }
        }
    }
    
    
} 

$contact_select->loadArray($contact_options);
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('');
$tpl->addBlockfile('contentmain','form', 'job.html');
$form->accept($renderer);

if ($job->getValue('is_template')==true) {
    $tpl->setVariable('title',_("Template"));
} elseif (isset($id)) {
    $tpl->setVariable('title',_("Job Posting"));
} else {
    $tpl->setVariable('title',_("New Job Posting"));
}


$tpl->show();

function checkDates($dates = array()) {
    $res = true;
    foreach ($dates[1] as $val) {
        $res = $val==0;
    }
    if ($res) {
        return true;
    }
    return Date::quickDate($dates[0])<Date::quickDate($dates[1]);    
}

function getStylesheets() {
    $handle=opendir ('xsl');
    $stylesheets = array();
    while (false !== ($file = readdir ($handle))) {
        if (ereg("(.*)\.xsl",$file,$res)) { 
            $stylesheets[$file] = $res[1];
        }
    }
    closedir($handle); 
    return $stylesheets;
}



?>