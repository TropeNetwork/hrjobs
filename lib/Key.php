<?php
class Key {

    private $name;
    private $group_id;
    
    public function __construct($group_id,$name) 
    {
        if (isset($group_id) && isset($name)) {
            $this->name=$name;
            $this->group_id=$group_id;
            $this->load();
        }
    }

    public function setValue($name,$value) {
        $this->values['active']=$value;
    }
    
    public function getValue($name) {
        return $this->values[$name];
    }
    
    private function load() {
        $db = Database::getConnection(DSN);
        $query="SELECT * 
                  FROM organization_group_keys 
                 WHERE group_id=".$this->group_id."
                   AND name='".$this->name."'";

        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }

    static function insert($group_id,$name,$value){
        $db = Database::getConnection(DSN);
        $query="REPLACE INTO  organization_group_keys 
                 (group_id,name,value,active)
                 VALUES
                 ('".$group_id."','".$name."','".$value."',false)";

        $result=$db->query($query);
    }

    private function delete(){
    }
    
    function setOrganization($org_id,$activate){
        $db = Database::getConnection(DSN);
        $query="REPLACE INTO  organization_keys 
                 (org_id,name,active)
                 VALUES
                 ('".$org_id."','".$this->name."','".$activate."')";
        $result=$db->query($query);
    }
    
    function getOrganization($org_id){
        $db = Database::getConnection(DSN);
        $query="SELECT active FROM organization_keys 
                WHERE org_id='".$org_id."'
                  AND name='".$this->name."'";
        $active=(bool) $db->getOne($query);
        return $active;
    }
    
 
    public function save() {
        $db = Database::getConnection(DSN);
        $query="REPLACE INTO  organization_group_keys 
                 (group_id,name,value,active)
                 VALUES
                 ('".$group_id."','".$name."','".$value."',$this->values['active'])";

        $result=$db->query($query);
    }
    
    /**
    * validate OHRwurm key
    **/
    public static function checkKey($key){
        $client = new SoapClient($GLOBALS['settings']['ohrwurm']['wsdl'],array('trace' =>1));
        try {
            $return = $client->checkKey($key);
        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
        } 
        echo ':::'.$return;
        return $return;
    }
}

?>