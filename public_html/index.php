<?php

include_once 'skin.inc';

$tpl->addBlockfile('contentmain','main', 'index.html');
$tpl->touchBlock('main');
$tpl->setVariable('title',_("Home"));
$tpl->show();
?>