<?php

include_once 'skin.inc';

$tpl->addBlockfile('contentmain','main', 'export.html');
$tpl->touchBlock('main');
$tpl->setVariable('title',_("Jobs Export"));
$tpl->show();

?>