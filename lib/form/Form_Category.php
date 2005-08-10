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

        $cat = new Categories($GLOBALS['settings']['i18n']['locale']);
        /*$industries = $cat->getCategories( 0,Categories::SECTION_INDUSTRY );
        foreach ($industries as $key=>$val){
            // Creates a checkboxes group using an array of separators
            $checkbox[] = &HTML_QuickForm::createElement('checkbox', $key, null, $val);
        }
        $this->addGroup($checkbox, 'category', _('Industry'), array('<td>', '</td>'));
        */
    }

}

?>