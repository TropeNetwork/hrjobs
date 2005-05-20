<?php
require_once 'Database.php';
require_once 'Contact.php';
require_once 'JobPosition.php';
require_once 'PostalAddress.php';

class HiringOrg {

    private $values = array();
    private $industries = array();
    public function __construct($id = null) {
        if (isset($id) && $id!=null) {
            $this->load($id);
        }
    }
    
    public function setValue($name, $value) {
        $this->values[$name] = $value;
    }
    public function getValue($name) {
        if (isset($this->values[$name])) { 
            return $this->values[$name];
        }
        return null;
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['org_id'])){
            $id = $db->nextId('org');
            $this->values['org_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO organization 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE organization SET $key='".addslashes($val)."' 
                             WHERE org_id=".$this->values['org_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        $this->saveIndustries();
        return($return);

    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['org_id'])){
            $query="DELETE FROM organization WHERE org_id=".$this->values['org_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM organization WHERE org_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
    
    public function getContacts() {
        $db = Database::getConnection(DSN);
        $query="SELECT contact_id FROM contact WHERE organization_org_id=".$this->values['org_id'];
        $data = $db->getCol($query);
        $contacts = array();
        if (isset($data)) {
            foreach($data as $id) {
                $contacts[$id] = new Contact($id);
            }
        }
        return $contacts;
    }
    
    public function getAddress() {
        $db = Database::getConnection(DSN);
        $query="SELECT address_id FROM postal_address WHERE organization_org_id=".$this->values['org_id'];
        $data = $db->getCol($query);
        if (isset($data) && !empty($data)) {
            $address = new PostalAddress($data[0]);            
        } else {
            $address = new PostalAddress();
            $address->setValue('organization_org_id',$this->getValue('org_id'));
            $address->save();    
             
        }   
        return $address;
    }
    
    public function getPublishedJobs() {
        $db = Database::getConnection(DSN);
        $query="SELECT job_id " .
                " FROM job_posting " .
                "WHERE organization_org_id=".$this->values['org_id'].
                "  AND start_date<='".date('Y-m-d',time())."'".
                "  AND job_status='active'" .
                "  AND is_template=0" .
                "  AND (end_date>'".date('Y-m-d',time())."' OR end_date='0000-00-00')";
        $data = $db->getCol($query);
        if (isset($data)) {
            foreach($data as $id) {
                $jobs[$id] = new JobPositionPosting($id);
            }
        }
        return $jobs;
    }
    
    public function setIndustries($industries) {
        $this->industries=$industries;
    }
    private function saveIndustries() {
        $db = Database::getConnection(DSN);
        $query="DELETE FROM organization_industries WHERE org_id=".$this->values['org_id'];
        $res = $db->query($query);
        foreach($this->industries as $profs) {
            if ($profs!=='') {
                $query="INSERT INTO organization_industries (org_id, industry_id) VALUES (".$this->values['org_id'].", ".$profs.")";
                $db->query($query);
            }
        }
    }
    public function getIndustries() {
        if (!isset($this->values['org_id'])) {
            return array();        
        }
        $db = Database::getConnection(DSN);
        $query="SELECT industry_id FROM organization_industries WHERE org_id=".$this->values['org_id'];
        $data = $db->getCol($query);
        if (isset($data)) {
            $this->industries = $data;            
        }
        return $this->industries;
    }
}

?>