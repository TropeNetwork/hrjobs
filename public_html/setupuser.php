<?php

require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

require_once 'OrgUser.php';
include_once 'configuration.inc';
require_once 'hradmin.config.inc';

$lu_dsn = array('dsn' => $dsn);
define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['groups']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['groups']['admins']);

$admin->perm->setCurrentApplication(HRADMIN_APP);
$admin->perm->outputRightsConstants(array(
    'prefix'       =>'HRJOBS_RIGHT',
    'area'         => HRADMIN_AREA,
    'application'  => HRADMIN_APP
),'php');

$users = $admin->getUsers();
$select[-1] = ""; 
if (is_array($users)) {
    foreach ($users as $user) {
        $select[$user['auth_user_id']] = $user['handle']; 
    }
}
$tpl =& new HTML_Template_Sigma('skins/default/');

$config = new Config;
$root =& $config->parseConfig(dirname(__FILE__).'/../config/config.xml', 'XML');

if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}

$settings = $root->toArray();
$settings = $settings['root']['conf'];
$initialized = $settings['setup']['initialized'];
if ($initialized) {
    if (!$usr->isLoggedIn()) {
        $tpl->loadTemplateFile('login.html');
        $tpl->setVariable('base',HTML_BASE);
        $tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
        $tpl->show();
        exit;
    } else {
        header('Location: index.php');
    }
}

$tpl->loadTemplateFile('setupuser.html');
$form = new HTML_QuickForm('setup','POST');

$form->addElement('text','admin', _("New User"),
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
    'admin' => 'admin'
);
$form->setDefaults($defaults);
$form->addRule('password', _("Password is required"), 'required');
$form->addRule(array('password', 'password2'), _("Passwords are not equal"), 'compare', null, 'server');
    
if ($form->validate()) {
    if ($form->exportValue('back')) {
        header("Location: setup.php");
        exit;
    }
    if ($form->exportValue('save')) {
        $new = notExistsUser($form->exportValue('admin'));
        $auth_id = 0;
        if ($new) {
            $perm_id = $admin->addUser(
                $form->exportValue('admin'),
                $form->exportValue("password"), 
                array(
                    'is_active'  => $form->exportValue("active"),
                ),
                array(
                    'name'  => "admin",
                    'email' => "admin@example.com"
                ),
                null,
                null 
            );
            if (DB::isError($perm_id)) {
                print_r($perm_id);
                unset($perm_id);
                exit;            
            }  
            $auth_id = getAuthUserId($perm_id); 
        } else {
            $user = getUser($form->exportValue('admin'));
            $auth_id = $user['auth_user_id'];  
            $perm_id = $user['perm_user_id'];
            $admin->updateUser(
                $perm_id, 
                $form->exportValue('admin'), 
                $form->exportValue('password'),
                array(
                    'is_active' => true
                ), 
                null);          
        }
        $res = $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>$settings['groups']['users']));
        if (PEAR::isError($res)) {
            print_r($res);
        }
        $res = $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>$settings['groups']['admins']));
        if (PEAR::isError($res)) {
            print_r($res);
        }
        
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
    if (!empty($users)) {
        foreach ($users as $user) {
            if (!empty($user) && $user['handle']===$handle) {
                return false;
            }    
        }    
    }
    return true;
}
function getUser($handle) {
    global $admin;
    $users = $admin->getUsers('perm');
    if (!empty($users)) {
        foreach ($users as $user) {
            if (!empty($user) && $user['handle']===$handle) {
                return $user;
            }    
        }    
    }
    return null;
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