<?php

require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

require_once 'hradmin.config.inc';

$lu_dsn = array('dsn' => $dsn);
define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['group']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['group']['admins']);

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
        if ($new!=='') {
            $id = $admin->addUser(
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
            if (DB::isError($id)) {
                print_r($id);
                unset($id);
                exit;            
            } else {
                $perm_id = getPermUserId($id);
            }   
        } else {
            $perm_id = getPermUserId($form->exportValue('admin'));
        }
        $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>HRADMIN_GROUP_USERS));
        $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>HRADMIN_GROUP_ADMINS));
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

function getPermUserId($user_id) {
    global $admin;
    $users = $admin->getUsers('perm',array('auth_user_id'=>$user_id));
    return $users[0]['perm_user_id'];    
}

?>