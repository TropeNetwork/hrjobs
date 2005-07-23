<?php
class Key {

    public function __construct($org_id,$name) 
    {
        if (isset($org_id) && isset($name)) {
            $this->load($org_id,$name);
        }
    }

    public function setValue($name,$value) {
    }
    
    public function getValue($name) {
    }
    
    private function load($org_id,$name) {
        $db = Database::getConnection(DSN);
        $query="SELECT * 
                  FROM organization_keys 
                 WHERE org_id=".$org_id."
                   AND name='".$name."'";

        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }

    static function insert($org_id,$name,$value){
        $db = Database::getConnection(DSN);
        $query="REPLACE INTO  organization_keys 
                 (org_id,name,value,active)
                 VALUES
                 ('".$org_id."','".$name."','".$value."',false)";

        $result=$db->query($query);
    }

    private function delete(){
    }  
 
    public function save() {
        $db = Database::getConnection(DSN);
    }
}

?>