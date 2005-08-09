<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITDynamic.php';
require_once 'Categories.php';

class Form_Language extends HTML_QuickForm {

    function __construct(){
        parent::__construct("language","GET");
        $this->addFields();  
    }
    
    private function addFields(){

        $checkbox[] = &HTML_QuickForm::createElement('checkbox', "de_DE", null, _("deutsch"));
        $checkbox[] = &HTML_QuickForm::createElement('checkbox', "en_US", null, _("englisch"));
        $this->addElement("select", 'language', _('Language'),array("a"=>"j"));
    }

}

?>