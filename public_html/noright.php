<?php
include_once 'skin.inc';
$tpl->addBlockfile('contentmain','main', 'noright.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', _("Error"));
$tpl->show();

?>