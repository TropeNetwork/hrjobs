<?php
require_once 'class/Database.php';
require_once 'configuration.inc';

class PostalAddress {

    private $values = array();
    
    public function __construct($id = null) {
        if (isset($id) && $id!=null) {
            $this->load($id);
        }
    }
    
    public function setValue($name, $value) {
        $this->values[$name] = $value;
    }
    public function getValue($name) {
        return $this->values[$name];
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['address_id'])){
            $id = $db->nextId('address');
            $this->values['address_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO postal_address 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE postal_address SET $key='".addslashes($val)."' 
                             WHERE address_id=".$this->values['address_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        return($return);
    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['address_id'])){
            $query="DELETE FROM postal_address WHERE address_id=".$this->values['address_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM postal_address WHERE address_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
}
?>