<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id: DynDate.php,v 1.1 2004/12/02 11:11:07 goetsch Exp $

require_once("HTML/QuickForm/date.php");
/**
 * Class to dynamically create HTML Select elements from a date
 *
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @access       public
 */
class HTML_QuickForm_dyndate extends HTML_QuickForm_date
{       
    /**
     * Class constructor
     * 
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $value          (optional)Input field value
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string 
     *                                      or an associative array. Date format is passed along the attributes.
     * @access    public
     * @return    void
     */
    function HTML_QuickForm_dyndate($elementName=null, $elementLabel=null, $options=array(), $attributes=null)
    {
        $this->HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'dyndate';
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if ('language' == $name) {
                    $this->_options['language'] = isset($this->_locale[$value])? $value: 'en';
                } elseif (isset($this->_options[$name])) {
                    if (is_array($value)) {
                        $this->_options[$name] = @array_merge($this->_options[$name], $value);
                    } else {
                        $this->_options[$name] = $value;
                    }
                }
            }
        }
    } //end constructor

    /**
     * Default javascript which calls the dynamic calender.
     * @access    private
     * @return    string   javascript, which invoces the dynamic calendar
     */
     function getJavascript(){
         return Date::getDynDateJS($this->getName());
     }

    /**
     * Returns the SELECT in HTML
     * @access    public
     * @return    string
     * @throws    
     */
    function toHtml()
    {
        $strHtml = '';
        foreach ($this->_elements as $key => $element) {
            if ($this->_flagFrozen) {
                if (is_string($element)) {
                    $strHtml .= str_replace(' ', '&nbsp;', $element);
                } else {
                    $strHtml .= $element->getFrozenHtml();
                }
            } else {
                if (is_string($element)) {
                    $strHtml .= str_replace(' ', '&nbsp;', $element);
                } else {
                    $element->setName($this->getName().'['.$element->getName().']');
                    $strHtml .= $element->toHtml();
                }                
            }
        } 
        $strHtml.=$this->getJavascript();
        return $strHtml;
    } // end func toHtml



} // end class HTML_QuickForm_date
?>