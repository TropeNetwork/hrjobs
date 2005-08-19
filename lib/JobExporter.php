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
		try{
			$exported_jobs = $client->listJobPostings($this->group->getValue('export_key'));
		} catch (SoapFault $fault) {
		    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
			echo $fault->faultstring;
		}
		$old_jobs = array();
		foreach ($exported_jobs as $exported_job) {
			$old_jobs[$exported_job->jobId]=array(
		    	'jobId'				=> $exported_job->jobId,
				'url'				=> $exported_job->url,
				'title'				=> $exported_job->title,
				'organizationId'	=> $exported_job->organizationId,
			);
		}
		
		$_jobs = array();
		foreach ($jobs as $job) {
			array_push($_jobs, array(
		    	'jobId'				=> $job->getValue('job_id'),
				'url'				=> HTML_BASE.'/jobview.php?id='.$job->getValue('job_id'),
				'title'				=> $job->getValue('job_title'),
				'organizationId'	=> $job->getValue('org_id'),
			));
			unset($old_jobs[$job->getValue('job_id')]);
		}
		if (count($old_jobs)>0) {
			try {
		        $result=$client->deleteJobPostings($this->group->getValue('export_key'),$old_jobs);
		    } catch (SoapFault $fault) {
		        trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
		        echo $fault->faultstring;
		    }
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
