<?php
require_once 'HiringOrg.php';
require_once 'Key.php';

class GroupKeys {

    private $orgs = null;
    private $values = array();
    private $group_id = -1;
    private $keys = array();
    /**
    * @param group_id
    **/
    public function __construct($id) 
    {
        if (isset($id)) {
            $this->load($id);
        }
    }

    function getKey($name){
        if (isset($this->keys[$name])){
            return $this->keys[$name];
        }
    }
    
    private function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT name FROM organization_group_keys WHERE group_id=".$id;
        $data=array();
        $result = $db->query($query);
        while($row=$result->fetchRow()){
            $this->keys[$row['name']]=new Key($id,$row['name']);
        }
        return $this->keys;
    }
}

?>