<?php

require_once 'configuration.inc';
require_once 'HTML/Template/Sigma.php';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
require_once 'class/HttpParameter.php';
require_once 'class/HiringOrg.php';

$tpl =& new HTML_Template_Sigma(TEMPLATE_DIR);
$tpl->loadTemplateFile('list.html');
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$org = new HiringOrg(HttpParameter::getParameter('org_id'));
$list = new DBTableList(DSN, 10);
$list->setTable('job_posting');
$list->setColumns(array (
    'job_id'                => 'Job Id.',
    'organization_org_id'   => 'Organisation',
    'job_title'             => 'Titel',
    'start_date'            => 'Start',
    'end_date'              => 'Ende'));

$list->orderby('job_id');
$list->where('organization_org_id='.HttpParameter::getParameter('org_id').
        " AND start_date<='".date('Y-m-d',time())."'".
        " AND job_status='active'" .
        " AND is_template=0" .
        " AND (end_date>'".date('Y-m-d',time())."' OR end_date='0000-00-00')");
$listrenderer = new DBTableList_Renderer_Sigma(
    & $tpl, 
    'joblist.html',
    'contentmain',
    'job'
);

$list->accept($listrenderer);

$tpl->setVariable('title', "Jobs bei ".$org->getValue('org_name'));
$tpl->setVariable('rss_link', '<link rel="alternate" type="application/rss+xml" title="RSS" href="'.HTML_BASE.'/rss/'.HttpParameter::getParameter('org_id').'/jobs.rss" />');
$tpl->show();
?>