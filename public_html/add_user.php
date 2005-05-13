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
if (!checkRights(HRJOBS_RIGHT_SYSTEM) 
  && (!$org_usr->getValue('is_group_admin')
  || !$org_usr->hasRightOnUser($id))) {
    header("Location: noright.php");
}
$group_id = HttpParameter::getParameter('groupid');

if (checkRights(HRJOBS_RIGHT_SYSTEM) && isset($group_id)) {
    $org_group = new OrgGroup($group_id);
} else {
    $org_group = new OrgGroup($org_usr->getGroupId());
}

        
$form = new HTML_QuickForm('user','POST');
$users = $admin->getUsers();
$select[-1] = ""; 
foreach ($users as $user) {
    $select[$user['auth_user_id']] = $user['handle']; 
}
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");

$form->addElement('select','user', _("Use exist User"),$select);
$form->addElement('checkbox','active', null, _("Enable"),
            array('class'=>'formFieldCheckbox'));
$form->addElement('checkbox','admin', null, _("Admin"),
            array('class'=>'formFieldCheckbox'));
$form->addElement('submit','save',_("Add"));

if (isset($group_id)) {
    $form->addElement('hidden', 'groupid', $group_id);
}
if (isset($id)) {
    $organization_user = new OrgUser($id);
    $defaults = array(
        'active'   => $user['is_active'],
        'admin'    => $organization_user->getValue('is_group_admin'),
    );
    $form->setDefaults($defaults);
}
$form->registerRule ('notexists', 'callback', 'notExistsUser');
if ($form->validate()) {
    $admin = new HRAdmin_Admin($objRightsAdminPerm); 
    if ($form->exportValue('save')) {
        $userid = $form->exportValue('user');
        $perm_id = getPermUserId($userid);
        $org_group->addUser($userid);
        $admin->addUserToGroup($perm_id);
        $organization_user = new OrgUser($userid);
        $organization_user->setValue('is_group_admin',$form->exportValue('admin'));
        $organization_user->save();
        header("Location: index.php");
        exit;
    } 
} 
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$tpl->addBlockfile('contentmain','form', 'add_user.html');
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