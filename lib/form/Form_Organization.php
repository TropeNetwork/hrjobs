<?php

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ITStatic.php';
require_once 'form/Form.php';
require_once 'Database.php';

class Form_Organization extends Form {
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
		$this->addElement('header','test',_("Contact"));
		$this->addElement('text','org_name', _("Name"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));
		$this->addElement('text','website', _("Website"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));      
		$this->addElement('textarea','org_description', _("Description"),
            array('rows'=>'10',
                  'cols'=>'70',
                  'wrap'=>'on',
                  'class'=>'formFieldTextArea'));

		$this->addElement('text','address', _("Address"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
		$this->addElement('text','street', _("Street"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
		$this->addElement('text','building_number', _("Building Number"),
            array('maxlength'=>'10',
                  'size'=>'3',
                  'class'=>'formFieldLong'));  
		$this->addElement('text','postal_code', _("Zip"),
            array('maxlength'=>'10',
                  'size'=>'5',
                  'class'=>'formFieldLong'));  
		$this->addElement('text','region', _("City"),
            array('maxlength'=>'100',
                  'size'=>'40',
                  'class'=>'formFieldLong'));  
		$this->addElement('text','country_code', _("Country"),
            array('maxlength'=>'30',
                  'size'=>'20',
                  'class'=>'formFieldLong'));  
		$this->file =& $this->addElement('file', 'logo', _("Logo"));
		$this->addElement('select','industry', _("Industry"),null,
		   array("size"=>"10",
                 "style"=>"width:200px;",
                 "multiple"=>"multiple") 
		); 
		$this->addElement('submit','save',_("Save"));
		$this->addElement('submit','delete',_("Delete"));
    }

}

?>