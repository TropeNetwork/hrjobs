<?php

require_once 'configuration.inc';
require_once 'HTML/Template/Sigma.php';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/HttpParameter.php';

$tpl =& new HTML_Template_Sigma(TEMPLATE_DIR);
$tpl->loadTemplateFile('list.html');
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);

$list = new DBTableList(DSN, 10);
$list->setTable('organization');
$list->setColumns(array (
    'org_id'    => 'Org Id.',
    'org_name'  => 'Organisation'
    ));

$list->orderby('org_id');
$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'organizationlist.html',
    'contentmain',
    'org'
);

$list->accept($listrenderer);

$tpl->setVariable('title', "Liste aller Organisationen");
$tpl->show();
?>