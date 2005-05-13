<?php

include_once 'skin.inc';
require_once 'DBTableList.php';
require_once 'DBTableList/Renderer/Sigma.php';
require_once 'Database.php';
if (!checkRights(HRJOBS_RIGHT_SYSTEM)) {
    header("Location: noright.php");
}
$tpl->addBlockfile('contentmain','main', 'admin.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', _("Administration"));
$tpl->show();

?>