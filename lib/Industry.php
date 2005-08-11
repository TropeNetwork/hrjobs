<?php
require_once 'Database.php';

class Industry {

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
        if (!isset($this->values['industry_id'])){
            $id = $db->nextId('industry');
            $this->values['industry_id'] = $id;
            $fields = '';
            $values = '';
            foreach($this->values as $field=>$value){
                $fields .= $field.', ';
                $values .= "'".addslashes($value)."', ";
            }
            $fields = substr($fields,0,strlen($fields)-2);
            $values = substr($values,0,strlen($values)-2);
            $query="INSERT INTO industry 
                           ($fields) 
                    VALUES ($values)";  
            $db->query($query);
            $return="added";
        }else{
            foreach($this->values as $key=>$val){
                $query="UPDATE industry SET $key='".addslashes($val)."' 
                             WHERE industry_id=".$this->values['industry_id'];
                $db->query($query);                
            }       
            $return="updated";
        }
        return($return);
    }
    
    public function delete() {
        $db = Database::getConnection(DSN);
        if (isset($this->values['industry_id'])){
            $query="DELETE FROM industry WHERE industry_id=".$this->values['industry_id'];
            $db->query($query);
            $return="removed";
        }
        return($return);
    }
    
    public function load($id) {
        $db = Database::getConnection(DSN);
        $query="SELECT * FROM industry WHERE industry_id=".$id;
        $data = $db->getRow($query);
        if (isset($data)) {
            foreach ($data as $name => $value) {
                $this->values[$name] = $value;
            }
        }
    }
}
?>