<?php
require_once 'Database.php';

class Profession {

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
        if (!isset($this->values['profession_id'])){
            $id = $db->nextId('profession');
            $this->values['profession_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO profession 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE profession SET $key='".addslashes($val)."' 
                             WHERE profession_id=".$this->values['profession_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        return($return);
    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['profession_id'])){
            $query="DELETE FROM profession WHERE profession_id=".$this->values['profession_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM profession WHERE profession_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
}
?>