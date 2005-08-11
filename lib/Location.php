<?php
require_once 'Database.php';

class Location {

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
        if (isset($this->values[$name])) { 
            return $this->values[$name];
        }
        return null;
    }
    
    public function save() {
        $db = Database::getConnection(DSN);
        if (!isset($this->values['location_id'])){
            $id = $db->nextId('location');
            $this->values['location_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO location 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE location SET $key='".addslashes($val)."' 
                             WHERE location_id=".$this->values['location_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        return($return);
    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['location_id'])){
            $query="DELETE FROM location WHERE location_id=".$this->values['location_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM location WHERE location_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
}
?>