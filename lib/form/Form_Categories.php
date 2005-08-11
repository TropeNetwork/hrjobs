<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'Categories.php';

class Form_Categories extends HTML_QuickForm {

    function __construct($name,$method){
        parent::__construct($name,$method);
        $this->addFields();  
    }
    
    private function addFields(){

        
    }

}

?>