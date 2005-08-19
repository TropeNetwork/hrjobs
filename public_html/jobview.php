<?php

require_once 'configuration.inc';
require_once 'JobPosition.php';
require_once 'XMLRenderer.php';
require_once 'HttpParameter.php';

$job = new JobPositionPosting(HttpParameter::getParameter('id'));


if (HttpParameter::getParameter('preview')==null) {
	if ($job->getValue('is_template')==1) {
		notFound();
		exit;
	} 
	if ($job->getValue('status')===JobPositionPosting::STATUS_INACTIVE) {
		gone();
		exit;
	}
	if ($job->getValue('status')===JobPositionPosting::STATUS_DELETED) {
		gone();
		exit;
	}
	if (strtotime($job->getValue('start_date'))>time()) {
		notFound();
		exit;
	}
	if ($job->getValue('end_date')!=='0000-00-00' && strtotime($job->getValue('end_date'))<time()) {
		gone();
		exit;
	}
}

header('Content-Type: text/html; charset=utf-8');
$renderer = new XMLRenderer();

$xml = $renderer->render($job);
$xsl = new DomDocument;
$xsl->load('xsl/'.$job->getValue('stylesheet'));

/* Configure the transformer */
$proc = new xsltprocessor;
$proc->importStyleSheet($xsl); // attach the xsl rules
echo $proc->transformToXML($xml); // actual transformation

function gone() {
	header('HTTP/1.0 410 Gone');
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>410 Gone</TITLE>
</HEAD><BODY>
<H1>Gone</H1>
The requested URL '.$_SERVER['REQUEST_URI'].' is gone on this server.<P>
<HR>
'.$_SERVER['SERVER_SIGNATURE'].'
</BODY></HTML>';	
}

function notFound() {
	header('HTTP/1.0 404 Not Found');
	echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<HTML><HEAD>
<TITLE>404 Not Found</TITLE>
</HEAD><BODY>
<H1>Not Found</H1>
The requested URL '.$_SERVER['REQUEST_URI'].' was not found on this server.<P>
<HR>
'.$_SERVER['SERVER_SIGNATURE'].'
</BODY></HTML>';	
}
?>