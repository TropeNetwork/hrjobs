<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'JobPosition.php';
require_once 'OrgUser.php';
require_once 'OrgGroup.php';
require_once 'Date.php';
require_once 'HttpParameter.php';
require_once 'HRAdmin/Admin.php';

$id = HttpParameter::getParameter('id');

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (!checkRights(HRADMIN_RIGHT_SYSTEM) 
  && (!$org_usr->getValue('is_group_admin')
  || !$org_usr->hasRightOnUser($id))) {
    header("Location: noright.php");
}
$group_id = HttpParameter::getParameter('groupid');

if (checkRights(HRADMIN_RIGHT_SYSTEM) && isset($group_id)) {
    $org_group = new OrgGroup($group_id);
} else {
    $org_group = new OrgGroup($org_usr->getGroupId());
}
if (isset($id)) {
$user = $objRightsAdminAuth->getUsers(array(
             'auth_user_id' => array(
                'name'  => 'auth_user_id',
                'op' => '=', 
                'value' => $id, 
                'cond' => '')
        ));
$user = $user[0];
} else {
    $user = array();
}
        
$form = new HTML_QuickForm('user','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");

$form->addElement('text','handle', _("Username"),
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
if (isset($id)) {
    $form->addElement('submit','delete',_("Delete"));
}

if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
}
if (isset($group_id)) {
    $form->addElement('hidden', 'groupid', $group_id);
}
$organization_user = new OrgUser($id);
$defaults = array(
    'name'     => $user['name'],
    'handle'   => $user['handle'],
    'email'    => $user['email'],
    'active'   => $user['is_active'],
    'admin'    => $organization_user->getValue('is_group_admin'),
);
$form->setDefaults($defaults);
$form->registerRule ('notexists', 'callback', 'notExistsUser');
$form->addRule('handle',    _("Username is required"), 'required');
$form->addRule('name',      _("Name is required"), 'required');
$form->addRule('email',     _("Email is required"), 'required');
$form->addRule('email',     _("Please enter a valid \"Email\""), 'email', null,'server');
if (!isset($id)) {
    $form->addRule('handle',    _("Username already exists"), 'notexists');
    $form->addRule('password',  _("Password is required"), 'required');
    $form->addRule('password2', _("Password is required"), 'required');
}
$form->addRule(array('password', 'password2'), _("Passwords are not equal"), 'compare', null, 'server');
if ($form->validate()) {
    $admin = new HRAdmin_Admin($objRightsAdminPerm); 
    if ($form->exportValue('save')) {
        if (isset($id)) {
            $pass = null;
            if ($form->exportValue("password")!='') {
                $pass = $form->exportValue("password");
            }            
            $objRightsAdminAuth->updateUser(
                $id,$form->exportValue('handle'), 
                $pass, 
                array(
                    'is_active'  => $form->exportValue("active"),
                ),
                array(
                    'name'  => $form->exportValue("name"),
                    'email' => $form->exportValue("email"),
                )
            );
            $admin->addUserToGroup(getPermUserId($id));
            $organization_user->setValue('is_group_admin',$form->exportValue('admin'));
            $organization_user->save();
            header("Location: users.php");
            exit;
        } else {
            $id = $objRightsAdminAuth->addUser(
                    $form->exportValue('handle'),
                    $form->exportValue("password"), 
                    array(
                        'is_active'  => $form->exportValue("active"),
                    ),
                    array(
                        'name'  => $form->exportValue("name"),
                        'email' => $form->exportValue("email")
                    ) 
            );
            if (DB::isError($id)) {
                print_r($id);
                unset($id);                
            } else {
                $perm_id = $objRightsAdminPerm->addUser($id,0);
                $org_group->addUser($id);
                $admin->addUserToGroup($perm_id);
                $organization_user = new OrgUser($id);
                $organization_user->setValue('is_group_admin',$form->exportValue('admin'));
                $organization_user->save();
                header("Location: users.php");
                exit;
            }            
        }
    } elseif ($form->exportValue('delete')) {
        $org_group->removeUser(getPermUserId($id));
        $objRightsAdminPerm->removeUser(getPermUserId($id));
        $objRightsAdminAuth->removeUser($id);
        header("Location: users.php");
        exit;
    }
} 
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$tpl->addBlockfile('contentmain','form', 'user.html');
$form->accept($renderer);

if (isset($id)) {
    $tpl->setVariable('title',_("Edit User"));
} else {
    $tpl->setVariable('title',_("New User"));
}


$tpl->show();

function getPermUserId($user_id) {
    global $objRightsAdminPerm;
    $users = $objRightsAdminPerm->getUsers($filters);
    foreach ($users as $user) {
        if ($user['auth_user_id']==$user_id) {
            return $user['perm_user_id'];
        }
    }
    return 0;
}
function notExistsUser($handle) {
    global $objRightsAdminAuth;
    $user = $objRightsAdminAuth->getUsers(array(
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
?>