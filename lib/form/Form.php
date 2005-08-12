<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';

abstract class Form extends HTML_QuickForm {

    function __construct($name,$method){
        parent::__construct($name,$method);
        $this->setRequiredNote('<font color=\"red\" size=\"1\"> *</font><font size=\"1\"> Pflichtfelder</font>');
        $this->addFields();  
        $this->setRules();
    }
    
    protected abstract function addFields();

	protected function setRules() {
		
	}
}

?>