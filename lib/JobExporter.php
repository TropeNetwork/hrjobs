<?php
require_once 'OrgGroup.php';
require_once 'HiringOrg.php';

class JobExporter {
	
	private $group;
	
	public function __construct($group) {
		$this->group = $group;
	}
	
	public function run() {
		$group = $this->group;
		$orgs = $group->getOrganizations(); 
		$client = new SoapClient($GLOBALS['settings']['ohrwurm']['wsdl'],array('trace' =>1));
		foreach ($orgs as $org) {
			if ($org->getValue('enable_export')==1) {
				$_org = array(
		    		'id'			=> $org->getValue('org_id'),
					'organization'	=> $org->getValue('org_name'),
				);
				try {
		    		$result=$client->setOrganization($this->group->getValue('export_key'),$_org);
				} catch (SoapFault $fault) {
				    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
					echo $fault->faultstring;
					print_r($client);
				}
				$this->exportJobs($org);
			}
		}
		
	}
	
	private function exportJobs($org) {
		$client = new SoapClient($GLOBALS['settings']['ohrwurm']['wsdl'],array('trace' =>1));
		$jobs = $org->getPublishedJobs();
		$_jobs = array();
		foreach ($jobs as $job) {
			array_push($_jobs, array(
		    	'jobId'				=> $job->getValue('job_id'),
				'url'				=> HTML_BASE.'/jobview.php?id='.$job->getValue('job_id'),
				'title'				=> $job->getValue('job_title'),
				'organizationId'	=> $job->getValue('org_id'),
			));

		}
		try {
		    $result=$client->setJobPostings($this->group->getValue('export_key'),$_jobs);
		} catch (SoapFault $fault) {
		    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
			echo $fault->faultstring;
		}
	}

}
?>
