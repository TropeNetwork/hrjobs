<?php

require_once 'Config.php';
require_once 'HTML/Template/Sigma.php';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

require_once 'hradmin.config.inc';
require_once 'LiveUser/Admin/Perm/Container/DB_Medium.php';
require_once 'LiveUser/Admin/Auth/Container/DB.php';
require_once 'LiveUser/Perm/Container/DB/Medium.php';
require_once 'class/HRAdmin/Admin.php';

$lu_dsn = array('dsn' => $dsn);
$adminAuth = new
        LiveUser_Admin_Auth_Container_DB(
            $lu_dsn, $conf['authContainers'][0]
        );
$adminPerm = new
        LiveUser_Admin_Perm_Container_DB_Medium($conf['permContainer']);
if (!$adminPerm->init_ok) {
    die('impossible to initialize: ' . $adminPerm->getMessage());
}
define('HRADMIN_APP',$settings['application']);
define('HRADMIN_AREA',$settings['area']);
define('HRADMIN_GROUP_USERS',$settings['group']['users']);
define('HRADMIN_GROUP_ADMINS',$settings['group']['admins']);

$adminPerm->setCurrentLanguage('DE');
$adminPerm->setCurrentApplication(HRADMIN_APP);
$adminPerm->outputRightsConstants(array('prefix'       =>'HRADMIN_RIGHT',
                                        'area'         => HRADMIN_AREA,
                                        'application'  => HRADMIN_APP),'php');

$users = $adminAuth->getUsers();
$select[-1] = ""; 
foreach ($users as $user) {
    $select[$user['auth_user_id']] = $user['handle']; 
}
$tpl =& new HTML_Template_Sigma('skins/default/');
$tpl->loadTemplateFile('setupuser.html');
$form = new HTML_QuickForm('setup','POST');

$form->addElement('select','admin', _("Vorhandenen User verwenden"),$select);
$form->addElement('text','new_admin', _("Neuen User anlegen"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('password', 'password', _("Password"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong'));
                  
$form->addElement('password', 'password2', _("Wiederholung"),
            array('maxlength'=>'10',
                  'class'=>'formFieldLong'));
$form->addElement('submit','back',_("Zurück"));
$form->addElement('submit','save',_("Beenden"));

$defaults = array(

);
$form->setDefaults($defaults);
$form->registerRule ('notexists', 'callback', 'notExistsUser');
$form->addRule('new_admin', "Login bereits vergeben!", 'notexists');
if(isset($_POST['new_admin']) && $_POST['new_admin']!=='') {
    $form->addRule('password', "Password darf nicht leer sein", 'required');
}
$form->addRule(array('password', 'password2'), 'Die Passwörter sind nicht gleich', 'compare', null, 'server');
    
if ($form->validate()) {
    if ($form->exportValue('back')) {
        header("Location: setup.php");
        exit;
    }
    if ($form->exportValue('save')) {
        $new = $form->exportValue('new_admin');
        if ($new!=='') {
            $id = $adminAuth->addUser(
                $form->exportValue('new_admin'),
                $form->exportValue("password"), 
                    array(
                        'is_active'  => $form->exportValue("active"),
                    ),
                    array(
                        'name'  => "admin",
                        'email' => ""
                    ) 
            );
            if (DB::isError($id)) {
                print_r($id);
                unset($id);                
            } else {
                $perm_id = $adminPerm->addUser($id,0);
            }   
        } else {
            $perm_id = getPermUserId($form->exportValue('admin'));
        }
        $admin = new HRAdmin_Admin($adminPerm); 
        $admin->addUserToGroup($perm_id);
        $admin->addUserToAdminGroup($perm_id);
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
    global $adminAuth;
    $user = $adminAuth->getUsers(array(
            'handle' => array(
                'name' => 'handle',
                'op' => '=', 
                'value' => $handle, 
                'cond' => '')
    ));
    if (!empty($user) && !empty($user[0])) {
        return false;
    }
    return true;
}
function getPermUserId($user_id) {
    global $adminPerm;
    $users = $adminPerm->getUsers($filters);
    foreach ($users as $user) {
        if ($user['auth_user_id']==$user_id) {
            return $user['perm_user_id'];
        }
    }
    return 0;
}
?>