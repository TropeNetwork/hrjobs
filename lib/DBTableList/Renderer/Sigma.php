<?php
require_once 'DBTableList/Renderer.php';
require_once 'Pager/Pager.php';

class DBTableList_Renderer_Sigma implements DBTableList_Renderer {
    private $tpl = null;
    private $block ;
    private $entry_block ;
    private $columnRenderer = null;
    private $rowRenderer = null;
    private $pager_params = array( 'prevImg'               => '<',
                                   'nextImg'               => '>',
                                   'separator'             => "",
                                   'linkClass'             => "pager",
                                   'spacesBeforeSeparator' => 1,
                                   'spacesAfterSeparator'  => 1,
                                   'lastPagePre'           => "",
                                   'lastPagePost'          => "",
                                   'lastPageText'          => '>>',
                                   'firstPagePre'          => "",
                                   'firstPagePost'         => "",
                                   'firstPageText'         => '<<',
                                   'curPageLinkClassName'  => "pagerCurrentLink",
                                   'clearIfVoid'           => "",
                                   'mode'                  => 'Sliding',
                                   'delta'                 => 2);
    function __construct(& $tpl, $listtpl, $place='contentmain', $block = 'liste', $entry_block = 'entry', & $columnRenderer = null, & $rowRenderer = null) {
        $this->tpl = $tpl;
        $this->block = $block;
        $this->entry_block = $entry_block;
        $this->tpl->addBlockfile($place, $block, $listtpl);
        if (isset($columnRenderer)) {
            $this->columnRenderer = $columnRenderer;
        }
        if (isset($rowRenderer)) {
            $this->rowRenderer = $rowRenderer;
        }
    }
    
    public function startTable($table) {
        foreach ($table->getColumns() as $col => $name) {
            $this->tpl->setVariable('th_'.$col,$name.$this->getOrderByLinks($col,$name));
        }
    }
    
    public function finishTable($table) {
        $this->renderPager($this->tpl, $table->getSize(), $table->getLimit());
    }
    
    public function renderRow($row) {
        $this->tpl->setCurrentBlock($this->block);
        if (isset($this->rowRenderer)) {
            $this->rowRenderer->renderRow(& $this->tpl,$row);
        }
        foreach ($row as $name => $col) {
            if (isset($this->columnRenderer)) {
                $this->tpl->setVariable($name, $this->columnRenderer->renderColumn($name,$col));
            } else {
                $this->tpl->setVariable($name, $col);
            }
        }
        $this->tpl->parse($this->entry_block);
    }
    
    private function renderPager(& $tpl, $totalItems, $perPage) 
    {
    	$this->tpl->setCurrentBlock($this->block);
        $this->pager_params['totalItems'] = $totalItems;
        $this->pager_params['perPage']    = $perPage;
        $this->pager_params['urlVar']     = $this->block.DBTableList::$parameter_page;
        $pager = & Pager::factory($this->pager_params);
        $data = $pager->getPageData();
        $array['links'] = $pager->getLinks();
        $array['totalItems'] = $totalItems;
        list ($array['from'], $array['to']) = $pager->getOffsetByPageId();
        $tpl->setVariable($this->block.'_from', $array['from']);
        $tpl->setVariable($this->block.'_next', $array['links']['next']);
        $tpl->setVariable($this->block.'_back', $array['links']['back']);
        $tpl->setVariable($this->block.'_pages', $array['links']['all']);
        $tpl->setVariable($this->block.'_to', $array['to']);
        $tpl->setVariable($this->block.'_totalItems', $array['totalItems']);
        //$this->tpl->touchBlock($this->block);
    }
    
    private function getOrderByLinks($col,$name) {
        return '&nbsp;<a href="'.$_SERVER['PHP_SELF'].
            $this->getLink(array($this->block.DBTableList::$parameter_orderby   => $col,
                                 $this->block.DBTableList::$parameter_direction => DBTableList::ascending)).
            '"><img src="'.IMAGES_DIR.'/static/ascending.gif" border="0" alt="Aufsteigend" width="11" height="11"></a>&nbsp;<a href="'.
            $_SERVER['PHP_SELF'].
            $this->getLink(array($this->block.DBTableList::$parameter_orderby   => $col,
                                 $this->block.DBTableList::$parameter_direction => DBTableList::descending)).
            '"><img src="'.IMAGES_DIR.'/static/descending.gif" border="0" alt="Absteigend" width="11" height="11"></a> ';
    }
    
    private function getLink($params = array()) {
        $parameters = array();
        foreach ($_GET as $p => $v) {
            $parameters[$p] = $v;
        }
        foreach ($params as $p => $v) {
            $parameters[$p] = $v;
        }
        $link = '?';
        foreach ($parameters as $p => $v) {
            $link .= $p.'='.$v.'&';
        }
        return $link;
    }
    
    public function setPagerOptions($options = array ()) 
    {
        foreach ($options as $name => $value) {
            $this->pager_params[$name] = $value;
        }
    }
    
}

interface DBTableList_Renderer_Sigma_ColumnRenderer {
    public function renderColumn($name,$column);
}
interface DBTableList_Renderer_Sigma_RowRenderer {
    public function renderRow(& $tpl, $row);
}


?>