<?php

include_once 'skin.inc';
require_once 'class/DBTableList.php';
require_once 'class/DBTableList/Renderer/Sigma.php';
require_once 'class/Database.php';
if (!checkRights(HRADMIN_RIGHT_SYSTEM)) {
    header("Location: noright.php");
}
$tpl->addBlockfile('contentmain','main', 'admin.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', "Administration");
$tpl->show();

?>