<?php

class DBTableList {
    private $dsn = null;
    private $limit = 20;
    private $columns = array();
    public $table = null;
    private $orderby = null;
    private $name = null;
    private $direction = null;
    private $actual_page = 0;
    private $size;
    private $where = null;
    
    public static $parameter_orderby = '_orderby';
    public static $parameter_direction = '_direction';
    public static $parameter_page = '_page';
    
    const ascending = 'ASC';
    const descending = 'DESC';
    
    function __construct($dsn, $limit = 20, $name='liste') {
        $this->dsn = $dsn;
        $this->limit = $limit;
        $this->name = $name;
    }
    
    public function setColumns($columns = array()) {
        $this->columns = $columns;
    }
    
    public function setTable($table) {
        $this->table = $table;
    }
    
    public function query($query = '') {
        $db = &Database::getConnection( $this->dsn );
        return $db->limitQuery($query,$this->actual_page*$this->limit,$this->limit);
    }
    
    private function createQuery() {
        $order = '';
        if (isset($this->orderby)) {
            $order = ' ORDER BY '.$this->orderby;
        }
        if (isset($this->direction)) {
            $order .= ' '.$this->direction;
        }
        
        return 'SELECT '.$this->createColumns().' FROM '.$this->table.$this->getWhereClause().$order; 
    }
    
    private function getWhereClause() {
        $where = '';
        if (isset($this->where)) {
            $where = ' WHERE '.$this->where;
        }
        return $where;
    }
    
    public function getSize() {
        if (!isset($size)) {
            $db = &Database::getConnection( $this->dsn  );
            $size = $db->getOne('SELECT COUNT(*) FROM '.$this->table.$this->getWhereClause());            
        }
        return $size;
    }
    
    private function createColumns() {
        $cols = '';   
        $s = '';     
        foreach ($this->columns as $column => $name) {
            $cols .= $s.$column;
            $s = ', ';
        }
        return $cols;        
    }
    
    public function orderby($col) {
        $this->orderby = $col;
    }
    
    public function where($where) {
        $this->where = $where;
    }
    
    private function processParameters() {
        if (isset($_GET[$this->name.self::$parameter_orderby])) {
            $this->orderby($_GET[$this->name.self::$parameter_orderby]);
        }
        if (isset($_GET[$this->name.self::$parameter_direction])) {
            $this->direction=$_GET[$this->name.self::$parameter_direction];
        }
        if (isset($_GET[$this->name.self::$parameter_page])) {
            $this->actual_page=$_GET[$this->name.self::$parameter_page];
            $this->actual_page--;
        }
    } 
    
    public function toHtml() {
        $renderer = $this->getDefaultRenderer();
        $this->accept(& $renderer);
        return $renderer->toHtml();
    }
    
    public function accept(& $renderer) {
        $this->processParameters();
        $renderer->startTable($this);
        $result = $this->query($this->createQuery());
        while ($row = $result->fetchRow()) {
            $renderer->renderRow($row);
        }
        $renderer->finishTable($this);
    }
    
   
    private function getDefaultRenderer() {
        require_once 'class/DBTableList/Renderer/Default.php';
        return new DBTableList_Renderer_Default();
    }
    
    public function getColumns() {
        return $this->columns;
    }
    public function getLimit() {
        return $this->limit;
    }
    public function getActualPage() {
        return $this->actualPage;
    }
}

?>