<?php
require_once 'XML/Tree.php';
require_once 'class/HiringOrg.php';

class XMLRenderer {

    public function __construct() {
    
    }
    
    public function render($job) {
        $horg = new HiringOrg($job->getValue('organization_org_id'));
        $address = $horg->getAddress();
        
        $tree = new XML_Tree();
        $root = $tree->addRoot('JobPositionPosting');
        $root->registerName('xsi','http://www.w3.org/2001/XMLSchema-instance');
        $org = $root->addChild('HiringOrg');
        
        $org->addChild('HiringOrgName',$horg->getValue('org_name'));
        $org->addChild('HiringOrgId',$horg->getValue('org_id'));
        $org->addChild('WebSite',$horg->getValue('website'));
        $contact = $org->addChild('Contact');
        
        $postalAddress = $contact->addChild('PostalAddress');
        $postalAddress->addChild('CountryCode',$address->getValue('country_code'));
        $postalAddress->addChild('PostalCode',$address->getValue('postal_code'));
        $postalAddress->addChild('Region',$address->getValue('region'));
        
        $deliveryAddress = $postalAddress->addChild('DeliveryAddress');
        if ($address->getValue('address')!='') {
            $deliveryAddress->addChild('AddressLine',$address->getValue('address'));
        }
        $deliveryAddress->addChild('StreetName',$address->getValue('street'));
        $deliveryAddress->addChild('BuildingNumber',$address->getValue('building_number'));
        
        $orgunit = $org->addChild('OrganizationalUnit');
        $orgunit->addChild('Description',$horg->getValue('org_description'));
        
        $detail = $root->addChild('PostDetail');
        $date = $detail->addChild('StartDate');
        $date->addChild('Date','1.1.2004');
        
        $info = $root->addChild('JobPositionInformation');
        $info->addChild('JobPositionTitle',$job->getValue('job_title'));
        $desc = $info->addChild('JobPositionDescription');
        $desc->addChild('SummaryText',$job->getValue('job_description'));
        $requ = $info->addChild('JobPositionRequirements');
        $requ->addChild('SummaryText',$job->getValue('job_requirements'));
        
        $contact = $job->getContact();
        $apply = $root->addChild('HowToApply');
        $appMethod = $apply->addChild('ApplicationMethods');
        if ($job->getValue('apply_by_email')) {
            $email = $appMethod->addChild('ByEmail');
            $email->addChild('E-Mail',$contact->getValue('email'));
        } 
        if ($job->getValue('apply_by_web')) {
            $web = $appMethod->addChild('ByWeb');
            $web->addChild('URL',$job->getValue('apply_by_web_url'));
        } 
        return $tree->get(true);
    }
}

?>