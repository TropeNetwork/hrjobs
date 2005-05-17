<?php

require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

require_once 'OrgUser.php';

require_once 'hradmin.config.inc';

$lu_dsn = array('dsn' => $dsn);
define('HRADMIN_APP',$hradmin_settings['application']);
define('HRADMIN_AREA',$hradmin_settings['area']);
define('HRADMIN_GROUP_USERS',$hradmin_settings['group']['users']);
define('HRADMIN_GROUP_ADMINS',$hradmin_settings['group']['admins']);

$admin->perm->setCurrentApplication(HRADMIN_APP);
$admin->perm->outputRightsConstants(array(
    'prefix'       =>'HRJOBS_RIGHT',
    'area'         => HRADMIN_AREA,
    'application'  => HRADMIN_APP
),'php');

$users = $admin->getUsers();
$select[-1] = ""; 
foreach ($users as $user) {
    $select[$user['auth_user_id']] = $user['handle']; 
}
$tpl =& new HTML_Template_Sigma('skins/default/');

$config = new Config;
$root =& $config->parseConfig(dirname(__FILE__).'/../config/config.xml', 'XML');

if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}

$settings = $root->toArray();
$settings = $settings['root']['conf'];
define('DSN','mysql://'.
             $settings['database']['user'].':'.
             $settings['database']['pass'].'@'.
             $settings['database']['host'].'/'.
             $settings['database']['name']);
$initialized = $settings['setup']['initialized'];
if ($initialized) {
    include_once 'configuration.inc';
    include_once 'hradmin.config.inc';
    if (!$usr->isLoggedIn()) {
        $tpl->loadTemplateFile('login.html');
        $tpl->setVariable('base',HTML_BASE);
        $tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
        $tpl->show();
        exit;
    } 
}

$tpl->loadTemplateFile('setupuser.html');
$form = new HTML_QuickForm('setup','POST');

$form->addElement('select','admin', _("Use exist User"),$select);
$form->addElement('text','new_admin', _("New User"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('password', 'password', _("Password"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong'));
                  
$form->addElement('password', 'password2', _("Repeat"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong'));
$form->addElement('submit','back',_("Back"));
$form->addElement('submit','save',_("Finish"));

$defaults = array(

);
$form->setDefaults($defaults);
$form->registerRule ('notexists', 'callback', 'notExistsUser');
$form->addRule('new_admin', _("Username already exists"), 'notexists');
if(isset($_POST['new_admin']) && $_POST['new_admin']!=='') {
    $form->addRule('password', _("Password is required"), 'required');
}
$form->addRule(array('password', 'password2'), _("Passwords are not equal"), 'compare', null, 'server');
    
if ($form->validate()) {
    if ($form->exportValue('back')) {
        header("Location: setup.php");
        exit;
    }
    if ($form->exportValue('save')) {
        $new = $form->exportValue('new_admin');
        $auth_id = 0;
        if ($new!=='') {
            $perm_id = $admin->addUser(
                $form->exportValue('new_admin'),
                $form->exportValue("password"), 
                array(
                    'is_active'  => $form->exportValue("active"),
                ),
                array(
                    'name'  => "admin",
                    'email' => ""
                ),
                null,
                null 
            );
            if (DB::isError($auth_id)) {
                print_r($auth_id);
                unset($auth_id);
                exit;            
            }  
            $auth_user_id = getAuthUserId($perm_id); 
        } else {
            $auth_id = $form->exportValue('admin');  
            $perm_id = getPermUserId($auth_id);          
        }
        
        $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>HRADMIN_GROUP_USERS));
        $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>HRADMIN_GROUP_ADMINS));
        
        // set the configuration to initialized
        $settings['setup']['initialized'] = true;
        $config = new Config;
        $root =& $config->parseConfig($settings, 'phparray');
        $res = $config->writeConfig(dirname(__FILE__).'/../config/config.xml', 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage().'</div>');
        }
        
        // redirect to index.php
        header("Location: index.php");
        exit;
    }
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$form->accept($renderer);

$tpl->setVariable('title',"Setup");
$tpl->show();

function notExistsUser($handle) {
    global $admin;
    $users = $admin->getUsers('perm');
    foreach ($users as $user) {
        if (!empty($user) && $user['handle']===$handle) {
            return false;
        }    
    }    
    return true;
}
function getAuthUserId($user_id) {
    global $admin;
    $users = $admin->getUsers('perm',array('perm_user_id'=>$user_id));
    return $users[0]['auth_user_id'];
}
function getPermUserId($user_id) {
    global $admin;
    $users = $admin->getUsers('perm',array('auth_user_id'=>$user_id));
    return $users[0]['perm_user_id'];    
}

?>