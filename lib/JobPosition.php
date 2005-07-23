<?php

require_once 'Database.php';

class JobPositionPosting 
{
    const STATUS_ACTIVE     = 'active';
    const STATUS_INACTIVE   = 'inactive';
    const STATUS_DELETED    = 'deleted';
    
    private $values = array();
    private $professions = array();
    private $locations = array();
    
    public function __construct($id = null) {
        if (isset($id)) {
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
    
    public function toXml() {
        return '';
    }
    
    public function postTemplate() {
        unset($this->values['job_id']);
        $this->setValue('is_template',false);
        $this->save();
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['job_id'])){
            $this->setValue('job_status',self::STATUS_ACTIVE);
            $id = $db->nextId('job');
            $this->values['job_id'] = $id;
            $fields = '';
            $values = '';
            if (!isset($this->values['job_reference']) or empty($this->values['job_reference'])){
              $this->values['job_reference']=$this->values['job_id'];
            }
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }

            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO job_posting 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);            
            $return="added";
        }else{
            if (!isset($this->values['job_reference']) or empty($this->values['job_reference'])){
              $this->values['job_reference']=$this->values['job_id'];
            }
            foreach($this->values as $key=>$val){
                $query="UPDATE job_posting SET $key='".addslashes($val)."' 
                             WHERE job_id=".$this->values['job_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        $this->saveProfessions();
        $this->saveLocations();
        return($return);
    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['job_id'])){
            
            $query="UPDATE job_posting SET job_status='".
                self::STATUS_DELETED.
                "' WHERE job_id=".$this->values['job_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }    
    
    public function disable() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['job_id'])){
            $query="UPDATE job_posting SET job_status='".
                self::STATUS_INACTIVE.
                "' WHERE job_id=".$this->values['job_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }    
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM job_posting WHERE job_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
    
    private function tag($name,$value) {
        return '<'.$name.'>'.$value.'</'.$name.'>'."\n";
    }

    public function getContact() {
        return new Contact($this->values['apply_contact_id']);
    }
    
    public function setProfessions($professions) {
        $this->professions=$professions;
    }
    private function saveProfessions() {
        $db = Database::getConnection(DSN);
        $query="DELETE FROM job_professions WHERE job_id=".$this->values['job_id'];
        $res = $db->query($query);
        foreach($this->professions as $profs) {
            if ($profs!=='') {
                $query="INSERT INTO job_professions (job_id, profession_id) VALUES (".$this->values['job_id'].", ".$profs.")";
                $db->query($query);
            }
        }
    }
    public function getProfessions() {
        if (!isset($this->values['job_id'])) {
            return array();        
        }
        $db = Database::getConnection(DSN);
        $query="SELECT profession_id FROM job_professions WHERE job_id=".$this->values['job_id'];
        $data = $db->getCol($query);
        if (isset($data)) {
            $this->professions = $data;            
        }
        return $this->professions;
    }
    
    public function setLocations($locations) {
        $this->locations=$locations;
    }
    private function saveLocations() {
        $db = Database::getConnection(DSN);
        $query="DELETE FROM job_locations WHERE job_id=".$this->values['job_id'];
        $res = $db->query($query);
        foreach($this->locations as $profs) {
            if ($profs!=='') {
                $query="INSERT INTO job_locations (job_id, location_id) VALUES (".$this->values['job_id'].", ".$profs.")";
                $db->query($query);
            }
        }
    }
    public function getLocations() {
        if (!isset($this->values['job_id'])) {
            return array();        
        }
        $db = Database::getConnection(DSN);
        $query="SELECT location_id FROM job_locations WHERE job_id=".$this->values['job_id'];
        $data = $db->getCol($query);
        if (isset($data)) {
            $this->locations = $data;            
        }
        return $this->locations;
    }
}

?>