<?php

include_once 'skin.inc';
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'class/JobPosition.php';
require_once 'class/OrgUser.php';
require_once 'class/OrgGroup.php';
require_once 'class/Date.php';
require_once 'class/HttpParameter.php';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/DBTableList/Renderer/User.php';

if (!checkRights(HRADMIN_RIGHT_SYSTEM)) {
    header("Location: noright.php");
}
$id = HttpParameter::getParameter('id');

$org_usr = new OrgUser($usr->getProperty('authUserId'));
if (isset($id)) {
    $group = new OrgGroup($id);
} else {
    $group = new OrgGroup();
}
        
$form = new HTML_QuickForm('group','POST');
$form->setRequiredNote("<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>");

$form->addElement('text','name', _("Name"),
            array('maxlength'=>'45',
                  'size'=>'30',
                  'class'=>'formFieldLong'));
$form->addElement('submit','save',_("Speichern"));
if (isset($id)) {
    if ($group->getValue('disabled')) {
        $form->addElement('submit','delete',_("Aktivieren"));
    } else {
        $form->addElement('submit','delete',_("Deaktivieren"));
    }
}
if (isset($id)) {
    $form->addElement('hidden', 'id', $id);
}
$defaults = array(
    'name'     => $group->getValue('group_name'),
);
$form->setDefaults($defaults);
$form->addRule('name', "Name darf nicht leer sein", 'required');
if ($form->validate()) {
    $group->setValue('group_name',$form->exportValue('name'));
    if ($form->exportValue('save')) {
        $group->save();
        header("Location: groups.php");
        exit;        
    } elseif ($form->exportValue('delete')) {
        if ($group->getValue('disabled')) {
            $group->enable();
        } else {
            $group->disable();
        }        
        header("Location: groups.php");
        exit;
    }
} 
$renderer =& new HTML_QuickForm_Renderer_ITStatic($tpl);
$renderer->setRequiredTemplate('{label}<font color="red" size="1"> *</font>');
$renderer->setErrorTemplate('<font color="orange" size="1">{error}</font><br/>{html}');
$tpl->addBlockfile('contentmain','form', 'group.html');
$form->accept($renderer);

if (isset($id)) {
    $tpl->setVariable('title',"Gruppe bearbeiten");
} else {
    $tpl->setVariable('title',"Neue Gruppe");
}

// Admins
$clist = new DBTableList(DSN, 10);
$clist->setTable('organization_user');
$clist->setColumns(array (
    'organization_user_id'    => '',
    'organization_group_id'   => '',
    'is_group_admin'          => ''
));
$clist->orderby('organization_user_id');
if (!isset($id)) {
    $id=0;
}
$clist->where('organization_group_id='.$id.' and is_group_admin=1');
$clistrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'users.html', 
    'users', 
    'user',
    new UserColumnRenderer(),
    new UserRowRenderer(& $objRightsAdminAuth)
);
$clist->accept($clistrenderer);
$tpl->setVariable('add_user','<a href="user.php?groupid='.$id.'"><img src="'.IMAGES_DIR.'/new.png" alt="Neuer Benutzer" /><br/>Neuer Benutzer</a>');
$tpl->show();

?>