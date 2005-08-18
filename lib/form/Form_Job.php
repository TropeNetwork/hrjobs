<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'form/Form.php';
require_once 'Database.php';

class Form_Job extends Form {
	private $file;
	
	public function getFile() {
		return $this->file;
	}
	
	protected function setRules() {
		$this->addRule('org_name',          _("Please enter a \"Name\" "), 'required', null,'server');
		$this->addRule('street',            _("Please enter a \"Street\" "), 'required', null,'server');
		$this->addRule('building_number',   _("Please enter a \"Building Number\" "), 'required', null,'server');
		$this->addRule('region',            _("Please enter a \"City\" "), 'required', null,'server');
		$this->addRule('postal_code',       _("Please enter a \"Zip\" "), 'required', null,'server');
		$this->addRule('country_code',      _("Please enter a \"Country\" "), 'required', null,'server');
		$this->addRule('website',           _("Please enter a \"Website\" "), 'required', null,'server');
		
	}
	
    protected function addFields() {
		$this->addElement('text','job_title', _("Job Titel"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
		$this->addElement('text','job_reference', _("Job Reference"),
            array('maxlength'=>'30',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
		$this->addElement('textarea','job_description', _("Description"),
            array('rows'=>'10',
                  'cols'=>'50',
                  'wrap'=>'on',
                  'class'=>'formFieldTextArea'));
		$this->addElement('textarea','job_requirements', _("Requirements"),
            array('rows'=>'10',
                  'cols'=>'50',
                  'wrap'=>'on',
                  'class'=>'formFieldTextArea'));
		$this->addElement('checkbox','byphone', null, _("Phone"),
            array('class'=>'formFieldCheckbox'));
		$this->addElement('checkbox','apply_by_email', null,_("Email"),
            array('checked'=>'true',
                  'class'=>'formFieldCheckbox'));   
		$this->addElement('checkbox','apply_by_web', null, _("Web"),
            array('class'=>'formFieldCheckbox'));
		$this->addElement('text','apply_by_web_url', _("URL:"),
            array('maxlength'=>'255',
                  'size'=>'60',
                  'class'=>'formFieldLong'));
        $this->addElement('select','profession', _("Profession"),null, 
           array("size"=>"10",
                 "style"=>"width:200px;",
                 "multiple"=>"multiple") );
		$this->addElement('select','location', _("Location"),null, 
           array("size"=>"10",
                 "style"=>"width:200px;",
                 "multiple"=>"multiple") );
    }

}

?>