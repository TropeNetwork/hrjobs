<?php

require_once 'DBTableList/Renderer.php';

class DBTableList_Renderer_Default implements DBTableList_Renderer {
    private $html = '';
    
    function __construct() {
        
    }
    
    public function startTable($table) {
        $this->html = "\n".'<table border="1"><tr>';
        foreach ($table->getColumns() as $col => $name) {
            $this->html .= '<th>'.$name.$this->getOrderByLinks($col,$name).'</th>';
        }
        $this->html .= '</tr>';
    }
    public function finishTable($table) {
        $this->html .= "\n".'</table>';
    }
    public function renderRow($row) {
        $this->html .= "\n".'<tr>';
        foreach ($row as $col) {
            $this->html .= '<td>'.$col.'</td>';
        }
        $this->html .= '</tr>';
    }  
    
    public function toHtml() {
        return $this->html;
    }
    
    private function getOrderByLinks($col,$name) {
        return '<a href="?'.DBTableList::$parameter_orderby.
            '='.
            $col.
            '&'.
            DBTableList::$parameter_direction.
            '='.DBTableList::ascanding.'">+</a><a href="?'.
            DBTableList::$parameter_orderby.
            '='.$col.'&'.
            DBTableList::$parameter_direction.
            '='.DBTableList::descanding.'">-</a> ';
    }
    
    private function getParameters() {
        return '?';
    }
}

?>