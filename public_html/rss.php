<?php
require_once 'configuration.inc';
require_once 'class/JobPosition.php';
require_once 'class/HiringOrg.php';
require_once 'XML/Tree.php';

if (isset($_SERVER['PATH_INFO'])){
  list(,$org,$rss)=split("/",$_SERVER['PATH_INFO']);
}else{
    echo $_SERVER['PATH_INFO'];
    exit;
}
$org = new HiringOrg($org);
$jobs = $org->getPublishedJobs();

$tree = new XML_Tree();
$root = $tree->addRoot('rdf:RDF');
$root->registerName('dc','http://purl.org/dc/elements/1.1/');
$root->registerName('h','http://www.w3.org/1999/xhtml');
$root->registerName('hr','http://www.w3.org/2000/08/w3c-synd/#');
$root->registerName('rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
$root->registerName('','http://purl.org/rss/1.0/');
$channel = $root->addChild('channel');
$channel->setAttribute('rdf:about',$rss);
$channel->addChild('title',"Jobs von ".$org->getValue('org_name'));
$channel->addChild('description',$org->getValue('org_description'));
$channel->addChild('link',HTML_BASE.'/');
$channel->addChild('dc:date',"date");
$items = $channel->addChild('items');
$rdfSeq = $items->addChild('rdf:Seq');

foreach ($jobs as $job) {
    $rdfLi = $rdfSeq->addChild('rdf:li');
    $rdfLi->setAttribute('rdf:resource','job#'.$job->getValue('job_id'));
    $item = $root->addChild('item');
    $item->setAttribute('rdf:about','job#'.$job->getValue('job_id'));
    $item->addChild('title',$job->getValue('job_title'));
    $item->addChild('description',$job->getValue('job_description'));
    $item->addChild('link',HTML_BASE.'/jobview.php?id='.$job->getValue('job_id'));
    $item->addChild('dc:date',"date");    
}

header("Content-Type: text/xml");
echo $tree->get(true);
?>