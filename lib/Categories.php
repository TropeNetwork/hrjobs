<?php

require_once 'Database.php';

class Categories {

    const SECTION_PROFESSION = 2;
    const SECTION_INDUSTRY   = 1;
    const SECTION_LOCATION   = 3;
    
    const LANG_DE        = 'de_DE'; 
    const LANG_US        = 'en_US'; 
    
    const LANG_FALLBACK  = LANG_US;
    
    private $language;
    
    function __construct($language="en_US"){
        $this->language=$language;
    }

    public function getCategories($id = 0, $type = self::SECTION_PROFESSION) {

        $db = Database::getConnection(DSN);
        $query="SELECT cat.cat_id
                  FROM cat
                 WHERE cat.section='$type'";
                   
        $res = $db->query($query);
        $cat = array();
        while($row = $res->fetchRow()) {
            $cat[$row['cat_id']] = $this->loadText($row['cat_id']);
        }
        return $cat;    
    }
    
    private function loadText($cat_id){
        $db = Database::getConnection(DSN);
        $query="SELECT text 
                  FROM cat_translation 
                 WHERE cat_id=$cat_id
                   AND language='$this->language'";
        $text=$db->getOne($query);
        if (empty($text)){
            $query="SELECT text 
                      FROM cat_translation 
                     WHERE cat_id=$cat_id
                       AND language='".LANG_FALLBACK."'";
        }
        $text=$db->getOne($query);
        return $text;
    }
}

?>