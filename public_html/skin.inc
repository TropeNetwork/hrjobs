<?php

require_once 'configuration.inc';
include_once 'i18n.inc';

require_once 'HTML/Template/Sigma.php';

require_once 'layersmenu/lib/PHPLIB.php';
require_once 'layersmenu/lib/layersmenu-common.inc.php';
require_once 'layersmenu/lib/layersmenu.inc.php';

if ($initialized!=1) {
    header("Location: setup/index.php");
    exit;
}

header('Content-Type: text/html; charset='.CHARSET);
$tpl =& new HTML_Template_Sigma(TEMPLATE_DIR);
$tpl->loadTemplateFile('template.html');

$tpl->setCallbackFunction('gettext','gettext');
$layersmenu = '<script language="JavaScript" type="text/javascript" src="/layersmenu/libjs/layersmenu-library.js"></script>'."\n";
$layersmenu .= '<script language="JavaScript" type="text/javascript" src="/layersmenu/libjs/layersmenu.js"></script>'."\n";
$mid = new LayersMenu(6, 7, 2, 1);

$mid->setMenuStructureString(getMenuString());
$mid->setIconsize(16, 16);
$mid->parseStructureForMenu('hormenu1');
$mid->newHorizontalMenu('hormenu1');
$layersmenu .= $mid->getHeader();
$tpl->setVariable('layersmenu_head',$layersmenu);
$tpl->setVariable('layersmenu',$mid->getMenu('hormenu1'));
$tpl->setVariable('layersmenu_footer',$mid->getFooter());

include_once 'hradmin.inc';
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$tpl->setVariable('loginName',$usr->getProperty('handle'));

if (!checkRights(HRJOBS_RIGHT_LOGIN)) {
    header("Location: ?logout=1");
    exit;
}

function getMenuString() {
    $menu = ".|"    .gettext("Home")."|index.php|".gettext("Home")."|home.png\n";
    $menu .= ".|"   .gettext("Organizations")."|organizations.php|".gettext("Oragnizations")."|organization.png\n";
    $menu .= "..|"  .gettext("Organizations")."|organizations.php|".gettext("Organizations")."|preview.png\n";
    $menu .= "..|"  .gettext("New Organization")."|organization.php|".gettext("New Organization")."|add_section.png\n";
    $menu .= ".|"   .gettext("Job Postings")."|jobs.php|".gettext("Job Postings")."|document.png\n";
    $menu .= "..|"  .gettext("Jobs")."|jobs.php|".gettext("Jobs")."|preview.png\n";
    $menu .= "..|"  .gettext("New Job")."|job.php|".gettext("New Job")."|edit.png\n";
    $menu .= "..|---\n";
    $menu .= "..|"  .gettext("Templates")."|jobs.php?templates=1|".gettext("Templates")."|template.png\n";
    $menu .= ".|"   .gettext("Settings")."|configuration.php|".gettext("Settings")."|configs.png\n";
    $menu .= "..|"  .gettext("Professions")."|professions.php|".gettext("Professions")."|configs.png\n";
    $menu .= "..|"  .gettext("User Administration")."|users.php|".gettext("User Administration")."|users.png\n";
    $menu .= "..|"  .gettext("Groups")."|groups.php|".gettext("Groups")."|groups.png\n";
    $menu .= "..|---\n";
    $menu .= "..|"  .gettext("Administration")."|admin.php|".gettext("Administration")."|configs.png\n";
    $menu .= "...|"  .gettext("Jobs Export")."|export.php|".gettext("Jobs Export")."|multisynk_16x16.png\n";
    $menu .= "...|"  .gettext("Categories")."|categories.php|".gettext("Categories")."|kcmdf_16x16.png\n";
    $menu .= ".|"   .gettext("Logout")."|?logout=1|".gettext("Logout")."|\n";
    $menu .= ".|"   .gettext("Help")."|about.php|".gettext("Help")."|help.png\n";
    $menu .= "..|"  .gettext("Support")."|about.php|".gettext("Support")."|help.png\n";
    $menu .= "..|---\n";
    $menu .= "..|"  .gettext("Info")."|about.php|".gettext("About HRjobs")."|info.png\n";
    return $menu;
}

?>