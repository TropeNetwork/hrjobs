<?php

require_once 'configuration.inc';
require_once 'JobPosition.php';
require_once 'XMLRenderer.php';
header('Content-Type: text/html; charset=utf-8');
$job = new JobPositionPosting($_GET['id']);
$renderer = new XMLRenderer();

$xml = $renderer->render($job);
$xsl = new DomDocument;
$xsl->load('xsl/'.$job->getValue('stylesheet'));

/* Configure the transformer */
$proc = new xsltprocessor;
$proc->importStyleSheet($xsl); // attach the xsl rules
echo $proc->transformToXML($xml); // actual transformation

?>