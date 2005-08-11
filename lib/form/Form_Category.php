<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'Categories.php';

class Form_Category extends HTML_QuickForm {

    function __construct($name,$method){
        parent::__construct($name,$method);
        $this->addFields();  
    }
    
    private function addFields(){

        $this->addElement(
			'text','name', 
			_("Name"),
            array(
				'maxlength'=>'100',
                'size'=>'40',
                'class'=>'formFieldLong'
            )
        );
        
        $this->addElement('submit','save',_("Save"));
		$this->addElement('submit','delete',_("Delete"));
    }

}

?>