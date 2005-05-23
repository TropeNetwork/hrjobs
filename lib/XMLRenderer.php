<?php
require_once 'XML/Tree.php';
require_once 'HiringOrg.php';

class XMLRenderer {

    public function __construct() {
    
    }
    
    public function render($job) {
        $horg = new HiringOrg($job->getValue('organization_org_id'));
        $address = $horg->getAddress();
        
        $dom_tree = new DomDocument('1.0', 'UTF-8');
        $dom_root = $dom_tree->createElement('JobPositionPosting');
        $dom_tree->appendChild($dom_root);
        $dom_org = $dom_tree->createElement('HiringOrg');
        $dom_root->appendChild($dom_org);
         
        $dom_org->appendChild($dom_tree->createElement('HiringOrgName',$horg->getValue('org_name')));
        $dom_org->appendChild($dom_tree->createElement('HiringOrgId',$horg->getValue('org_id')));
        $dom_org->appendChild($dom_tree->createElement('WebSite',$horg->getValue('website')));
        $dom_contact = $dom_tree->createElement('Contact');
        $dom_org->appendChild($dom_contact);
        
        $dom_pa = $dom_tree->createElement('PostalAddress');
        $dom_contact->appendChild($dom_pa);
        $dom_pa->appendChild($dom_tree->createElement('CountryCode',$address->getValue('county_code')));
        $dom_pa->appendChild($dom_tree->createElement('PostalCode',$address->getValue('postal_code')));
        $dom_pa->appendChild($dom_tree->createElement('Region',$address->getValue('region')));
        
        $dom_da = $dom_tree->createElement('DeliveryAddress');
        $dom_pa->appendChild($dom_da);
        if ($address->getValue('address')!='') {
            $dom_da->appendChild($dom_tree->createElement('AddressLine',$address->getValue('address')));
        }
        $dom_da->appendChild($dom_tree->createElement('StreetName',$address->getValue('street')));
        $dom_da->appendChild($dom_tree->createElement('BuildingNumber',$address->getValue('building_number')));
        
        $dom_orgunit = $dom_tree->createElement('OrganizationalUnit');
        $dom_org->appendChild($dom_orgunit);
        $dom_orgunit->appendChild($dom_tree->createElement('Description',$horg->getValue('org_description')));

        $dom_detail = $dom_tree->createElement('PostDetail');
        $dom_root->appendChild($dom_detail);
        $dom_date = $dom_tree->createElement('StartDate');
        $dom_detail->appendChild($dom_date);
        $dom_date->appendChild($dom_tree->createElement('Date','1.1.2004'));
        
        $dom_info = $dom_tree->createElement('JobPositionInformation');
        $dom_root->appendChild($dom_info);
        $dom_info->appendChild($dom_tree->createElement('JobPositionTitle',$job->getValue('job_title')));
        $dom_desc = $dom_tree->createElement('JobPositionDescription');
        $dom_info->appendChild($dom_desc);
        $dom_desc->appendChild($dom_tree->createElement('SummaryText',$job->getValue('job_description')));
        $dom_desc = $dom_tree->createElement('JobPositionRequirements');
        $dom_info->appendChild($dom_desc);
        $dom_desc->appendChild($dom_tree->createElement('SummaryText',$job->getValue('job_requirements')));
        
        $contact = $job->getContact();
        
        $dom_apply = $dom_tree->createElement('HowToApply');
        $dom_root->appendChild($dom_apply);
        $dom_appMethod = $dom_tree->createElement('ApplicationMethods');
        $dom_apply->appendChild($dom_appMethod);
        if ($job->getValue('apply_by_email')) {
            $dom_email = $dom_tree->createElement('ByEmail');
            $dom_appMethod->appendChild($dom_email);
            $dom_email->appendChild($dom_tree->createElement('E-Mail',$contact->getValue('email')));            
        } 
        if ($job->getValue('apply_by_web')) {
            $dom_email = $dom_tree->createElement('ByWeb');
            $dom_appMethod->appendChild($dom_email);
            $dom_email->appendChild($dom_tree->createElement('URL',$job->getValue('apply_by_web_url')));            
        } 
        return $dom_tree;
    }
}

?>