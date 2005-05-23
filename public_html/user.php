<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'JobPosition.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'Date.php';
require_once 'HttpParameter.php';

$auth_user_id = HttpParameter::getParameter('id');

$org_usr = new OrgUser($usr->getProperty('authUserId'));

if (!checkRights(HRJOBS_RIGHT_SYSTEM) 
  && (!$org_usr->getValue('is_group_admin')
  || (isset($auth_user_id) && !$org_usr->hasRightOnUser($auth_user_id)))) {
    header("Location: noright.php");
    exit;
}
$group_id = HttpParameter::getParameter('groupid');


if (isset($auth_user_id)) {
    $user = $admin->getUsers('perm',array('auth_user_id' => $auth_user_id));
    $user = $user[0];
} else {
    $user = array();
}
        
$form = new HTML_QuickForm('user','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");

if (checkRights(HRJOBS_RIGHT_SYSTEM)) {
    $select_group = getGroups();
    $form->addElement('select','group', _("Gruppe"),$select_group);
    $form->addRule('group', _("Please select a \"Group\" "), 'required', null,'server');
} else {
    $org_group = new OrgGroup($org_usr->getGroupId());
}
if (isset($group_id)) {
    $org_group = new OrgGroup($group_id);
} 
$form->addElement('text','login', _("Username"),
            array('maxlength'=>'10',
                  'size'=>'10',
                  'class'=>'formFieldLong'));
$form->addElement('text','name', _("Name"),
            array('maxlength'=>'20',
                  'size'=>'20',
                  'class'=>'formFieldLong'));
$form->addElement('text','email', _("Email"),
            array('maxlength'=>'40',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
$form->addElement('password', 'password', _("Password"));
$tpl->setVariable(array('maxlength'=>'10',
                        'class'=>'formFieldLong'));
                  
$form->addElement('password', 'password2', _("Repeat"));
$tpl->setVariable(array('maxlength'=>'10',
                        'class'=>'formFieldLong'));
$form->addElement('checkbox','active', null, _("Enable"),
            array('class'=>'formFieldCheckbox'));
$form->addElement('checkbox','admin', null, _("Admin"),
            array('class'=>'formFieldCheckbox'));
$form->addElement('submit','save',_("Save"));
if (isset($auth_user_id)) {
    $form->addElement('submit','delete',_("Delete"));
}

if (isset($auth_user_id)) {
    $form->addElement('hidden', 'id', $auth_user_id);
}
$defaults = array();
if (isset($group_id)) {
    $form->addElement('hidden', 'groupid', $group_id);
    $defaults['group'] =  $group_id;
}

if (isset($auth_user_id)) {
    $organization_user = new OrgUser($auth_user_id);
    $defaults['name']   = $user['name'];
    $defaults['login']  = $user['handle'];
    $defaults['email']  = $user['email'];
    $defaults['active'] = $user['is_active'];
    $defaults['admin']  = $organization_user->getValue('is_group_admin');
    $defaults['group'] =  $organization_user->getValue('organization_group_id');
}

$form->setDefaults($defaults);

if (!isset($_POST['delete'])) {
    $form->registerRule ('notexists', 'callback', 'notExistsUser');
    $form->addRule('login',    _("Username is required"), 'required');
    $form->addRule('name',      _("Name is required"), 'required');
    $form->addRule('email',     _("Email is required"), 'required');
    $form->addRule('email',     _("Please enter a valid \"Email\""), 'email', null,'server');

    if (!isset($auth_user_id)) {
        $form->addRule('login',    _("Username already exists"), 'notexists');
        $form->addRule('password',  _("Password is required"), 'required');
        $form->addRule('password2', _("Password is required"), 'required');
    }
}

$form->addRule(array('password', 'password2'), _("Passwords are not equal"), 'compare', null, 'server');
if ($form->validate()) {
     
    if ($form->exportValue('save')) {
        if (isset($auth_user_id)) {
            $pass = null;
            if ($form->exportValue("password")!='') {
                $pass = $form->exportValue("password");
            }            
            $admin->updateUser(
                $auth_user_id,
                $form->exportValue('login'), 
                $pass, 
                array(
                    'is_active'  => $form->exportValue("active"),
                ),
                array(
                    'name'  => $form->exportValue("name"),
                    'email' => $form->exportValue("email"),
                ),
                null,
                null
            );
            
            $group_id = $form->getSubmitValue("group");
            if (isset($group_id)) {
                $organization_user->setValue('organization_group_id',$group_id);
            }
            $admin->perm->addUserToGroup(array('perm_user_id'=>getPermUserId($auth_user_id),'group_id'=>HRADMIN_GROUP_USERS));
            $organization_user->setValue('is_group_admin',$form->exportValue('admin'));
            
            $organization_user->save();
            header("Location: users.php");
            exit;
        } else {
            $perm_id = $admin->addUser(
                $form->exportValue('login'),
                $form->exportValue("password"), 
                array(
                    'is_active'  => $form->exportValue("active"),
                ),
                array(
                    'name'  => $form->exportValue("name"),
                    'email' => $form->exportValue("email")
                ),
                null,
                null
            );
            if (DB::isError($auth_user_id)) {
                print_r($auth_user_id);
                unset($auth_user_id);
                exit;                
            }
            $group_id = $form->getSubmitValue("group");
            if (isset($group_id)) {
                $org_group = new OrgGroup($group_id);
            }
            $auth_user_id = getAuthUserId($perm_id);
            $org_group->addUser($auth_user_id);
            $admin->perm->addUserToGroup(array('perm_user_id'=>$perm_id,'group_id'=>HRADMIN_GROUP_USERS));
            $organization_user = new OrgUser($auth_user_id);
            $organization_user->setValue('is_group_admin',$form->exportValue('admin'));
            $organization_user->save();
            
            header("Location: users.php");
            exit;
                        
        }
    } elseif ($form->exportValue('delete')) {
        $org_group->removeUser($auth_user_id);
        $admin->removeUser(getPermUserId($auth_user_id));
        header("Location: users.php");
        exit;
    }
} 
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$tpl->addBlockfile('contentmain','form', 'user.html');
$form->accept($renderer);

if (isset($auth_user_id)) {
    $tpl->setVariable('title',_("Edit User"));
} else {
    $tpl->setVariable('title',_("New User"));
}


$tpl->show();

function getPermUserId($user_id) {
    global $admin;
    $users = $admin->getUsers('perm',array('auth_user_id'=>$user_id));
    return $users[0]['perm_user_id'];
}

function getAuthUserId($user_id) {
    global $admin;
    $users = $admin->getUsers('perm',array('perm_user_id'=>$user_id));
    return $users[0]['auth_user_id'];
}

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