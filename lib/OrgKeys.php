<?php
require_once 'HiringOrg.php';
require_once 'Key.php';

class OrgKeys {

    private $orgs = null;
    private $values = array();
    private $group_id = -1;
    private $keys = array();
    /**
    **/
    public function __construct($id) 
    {
        if (isset($id)) {
            $this->load($id);
        }
    }

#    public function getKeys() {
#        return $this->load();
#    }
    
    private function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT name FROM organization_keys WHERE org_id=".$id;
        $data=array();
        $result = $db->query($query);
        while($row=$result->fetchRow()){
            array_push($this->keys,new Key($id,$row['name']));
        }
        return $this->keys;
    }
}

?>