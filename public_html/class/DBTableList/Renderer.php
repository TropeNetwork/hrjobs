<?php

interface DBTableList_Renderer {
    public function startTable($table);
    public function finishTable($table);
    public function renderRow($row);    
}

?>