<?php

include_once '../configuration.inc';
require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'OrgGroup.php';
require_once 'Database.php';
require_once 'OrgUser.php';
require_once 'LiveUserConfiguration.php';
$luConfig = new LiveUserConfiguration($settings);
$admin = $luConfig->getAdmin();

$lu_dsn = array('dsn' => DSN );
define('HRADMIN_APP',@$settings['application']);
define('HRADMIN_AREA',@$settings['area']);
define('HRADMIN_GROUP_USERS',@$settings['groups']['users']);
define('HRADMIN_GROUP_ADMINS',@$settings['groups']['admins']);

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
$tpl =& new HTML_Template_Sigma('../skins/default/');

$config = new Config;
$root =& $config->parseConfig(dirname(__FILE__).'/../../config/config.xml', 'XML');

if (PEAR::isError($root)) {
    die('Error while reading configuration: ' . $root->getMessage());
}

$settings = $root->toArray();
$settings = $settings['root']['conf'];
$initialized = $settings['setup']['initialized'];
if ($initialized) {
    header('Location: ../index.php');
    exit;
}

$tpl->loadTemplateFile('setupuser.html');
$form = new HTML_QuickForm('setup','POST');

$form->addElement('text','admin', _("New User"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong',
                  'tabindex'=>0 ));
$form->addElement('password', 'password', _("Password"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong',
                  'tabindex'=>1 ));
                  
$form->addElement('password', 'password2', _("Repeat"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong',
                  'tabindex'=>2 ));
$form->addElement('submit','save',_("Finish"),array('tabindex'=>3));
$form->addElement('submit','back',_("Back"),array('tabindex'=>4));


$defaults = array(
    'admin' => 'admin'
);
$form->setDefaults($defaults);
$form->addRule('password', _("Password is required"), 'required');
$form->addRule(array('password', 'password2'), _("Passwords are not equal"), 'compare', null, 'server');
    
if ($form->exportValue('back')) {
    header("Location: setup.php");
    exit;
}
if ($form->validate()) {
    
    if ($form->exportValue('save')) {
        $new = notExistsUser($form->exportValue('admin'));
        $auth_id = 0;
        if ($new) {
            $data = array(
                'handle'        => $form->exportValue('admin'),
                'is_active'     => true,
                'name'          => "admin",
                'email'         => "admin@example.com",
                'passwd'        => $form->exportValue("password")
            );
            $perm_id = $admin->addUser($data);
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
            $data = array(
                'handle'        => $form->exportValue('admin'),
                'is_active'     => true,
                'passwd'        => $form->exportValue("password")
            );
            $admin->updateUser(
                $perm_id, 
                $data);          
        }
        $res = $admin->perm->addUserToGroup(array(
			'perm_user_id'	=> $perm_id,
			'group_id'		=> @$settings['groups']['users']
		));
        if (PEAR::isError($res)) {
            print_r($res);
        }
        $res = $admin->perm->addUserToGroup(array(
			'perm_user_id'	=> $perm_id,
			'group_id'		=> @$settings['groups']['admins']
		));
        if (PEAR::isError($res)) {
            print_r($res);
        }
        $db = Database::getConnection(DSN);
        $res = $db->getOne('SELECT group_id FROM organization_group WHERE group_name=\'Admin Group\'');
        $admin_group_id = null;
        if ($res!==null) {
        	$admin_group_id = $res;
        }
        $org_group =  new OrgGroup($admin_group_id);
        $org_group->setValue('group_name','Admin Group');
        $org_group->save();
        $org_group->enable();
        $org_group->addUser($auth_id);
        $organization_user = new OrgUser($auth_id);
        $organization_user->setValue('is_group_admin',true);
        $organization_user->save();
        // set the configuration to initialized
        $settings['setup']['initialized'] = true;
        $config = new Config;
        $root =& $config->parseConfig($settings, 'phparray');
        $res = $config->writeConfig(_HRJOBS_CONFIG_FILE, 'XML');
        if (PEAR::isError($res)) {
            $tpl->setVariable('errors','<div class="error">'.$res->getMessage().'</div>');
        }
        
        // redirect to index.php
        header("Location: ../index.php");
        exit;
    }
}

$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$form->accept($renderer);

$tpl->setVariable('title',"Setup Step 3 of 3");
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