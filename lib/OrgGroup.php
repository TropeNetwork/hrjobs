<?php
require_once 'HiringOrg.php';
class OrgGroup {
    private $values = array();
    private $orgs = array();
    public function __construct($id=null) 
    {
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
    
    public function getOrganizations() {
        if ($this->orgs==null) {
            $db = Database::getConnection(DSN);
            $query="SELECT org_id " .
            		" FROM organization org ".
                    "WHERE group_id=".(int)$this->values['group_id'];
            $data = $db->getCol($query);
            foreach($data as $id) {
                $this->orgs[$id] = new HiringOrg($id);
            }
        }
        return $this->orgs;
    }
    
    public function addUser($userid) {
        $db = Database::getConnection(DSN);
        $res = $db->getOne('SELECT group_id FROM organization_user WHERE organization_user_id='.$userid);
        if ($res!==null) {
            $query='UPDATE organization_user SET group_id='.(int)$this->getValue('group_id').' WHERE organization_user_id='.$userid;
        } else {
            $query="  INSERT 
                    INTO organization_user 
                         (organization_user_id, group_id) 
                  VALUES (".$userid.", ".(int)$this->getValue('group_id').")";
        }
        $db->query($query);
    }
    
    public function removeUser($userid) {
        $db = Database::getConnection(DSN);
        $query="  DELETE 
                    FROM organization_user 
                   WHERE organization_user_id=".$userid;
        $db->query($query);
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['group_id'])){
            $id = $db->nextId('group');
            $this->values['group_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO organization_group 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);            
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE organization_group SET $key='".addslashes($val)."' 
                             WHERE group_id=".$this->values['group_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        return($return);
    }
    
    public function disable() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['group_id'])){
            
            $query="UPDATE organization_group SET disabled=1".
                " WHERE group_id=".$this->values['group_id'];
            $db->query($query);
            $return="diabled";
        }
        return($return);
    }    
    public function enable() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['group_id'])){            
            $query="UPDATE organization_group SET disabled=0".
                " WHERE group_id=".$this->values['group_id'];
            $db->query($query);
            $return="enabled";
        }
        return($return);
    }    
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM organization_group WHERE group_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
}

?>