<?php

include_once 'skin.inc';

$tpl->addBlockfile('contentmain','main', 'about.html');
$tpl->touchBlock('main');
$tpl->setVariable('title', "Infos zu HRJobs");
$tpl->show();


?>