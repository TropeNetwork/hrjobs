<?php
require_once 'configuration.inc';
require_once 'HTML/Template/Sigma.php';
require_once 'layersmenu/lib/PHPLIB.php';
require_once 'layersmenu/lib/layersmenu-common.inc.php';
require_once 'layersmenu/lib/layersmenu.inc.php';
$tpl =& new HTML_Template_Sigma(TEMPLATE_DIR);
$tpl->loadTemplateFile('template.html');
$tpl->setVariable("base",HTML_BASE);
$tpl->setVariable('theme',HTML_BASE.'/'.THEME_BASE.'/'.THEME_SKIN);
$layersmenu = '<script language="JavaScript" type="text/javascript" src="layersmenu/libjs/layersmenu-library.js"></script>'."\n";
$layersmenu .= '<script language="JavaScript" type="text/javascript" src="layersmenu/libjs/layersmenu.js"></script>'."\n";
$mid = new LayersMenu(6, 7, 2, 1);
$mid->setMenuStructureFile('menu.txt');
$mid->setIconsize(16, 16);
$mid->parseStructureForMenu('hormenu1');
$mid->newHorizontalMenu('hormenu1');
$layersmenu .= $mid->getHeader();
$tpl->setVariable('layersmenu_head',$layersmenu);
$tpl->setVariable('layersmenu',$mid->getMenu('hormenu1'));
$tpl->setVariable('layersmenu_footer',$mid->getFooter());
include_once 'hradmin.inc';
if (!checkRights(HRADMIN_RIGHT_LOGIN)) {
    header("Location: noright.php");
}
?>