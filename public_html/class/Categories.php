<?php

class Categories {
    const TYPE_PROFESSION = 'profession';
    const TYPE_INDUSTRY = 'industry';
    const TYPE_LOCATION = 'location';
    
    public static function getCategories($id = 0, $type = self::TYPE_PROFESSION) {
        require_once 'class/Database.php';
        $db = Database::getConnection(DSN);
        $query="SELECT name, ".$type."_id FROM ".$type." WHERE parent_".$type."_id=".$id;
        $res = $db->query($query);
        $cat = array();
        while($row = $res->fetchRow()) {
            $cat[$row[$type.'_id']] = $row['name'];
        }
        return $cat;    
    }
    
    public static function getAllSubCategories($id = 0, $type = self::TYPE_PROFESSION) {
        $category = self::getCategories($id,$type);
        $subCategories = array();
        foreach ($category as $k => $v) {
            $cat = self::getCategories($k,$type);
            foreach($cat as $ck => $cv) {
                $subCategories[$k][$ck] = $cv;    
            }
        }
        return $subCategories;
    }
    
    public static function getAllCategories($id = 0, $type = self::TYPE_PROFESSION) {
        $category = self::getCategories($id,$type);
        $subCategories = array();
        foreach ($category as $k => $v) {
            $subCategories[$k] = $v;
            $cat = self::getAllCategories($k,$type);
            foreach($cat as $ck => $cv) {
                $subCategories[$ck] = $cv;    
            }            
        }
        return $subCategories;
    }
    
    public static function getParentCategory($id, $type = self::TYPE_PROFESSION) {
        require_once 'class/Database.php';
        $db = Database::getConnection(DSN);
        $query="SELECT parent_".$type."_id FROM ".$type." WHERE ".$type."_id=".$id;
        $res = $db->getOne($query);
        return $res===null?0:$res;    
    }
    
    public static function getCategoryValues($ids = array(),$type = self::TYPE_PROFESSION) {
        $res = array();
        foreach ($ids as $id) {
            $res[$id] = self::getValue($id, $type);
        }
        return $res;
    }
   
    private static function getValue($id, $type = self::TYPE_PROFESSION) {
        require_once 'class/Database.php';
        $db = Database::getConnection(DSN);
        $query="SELECT name FROM ".$type." WHERE ".$type."_id=".$id;
        $res = $db->getOne($query);
        return $res===null?"":$res;
    }
}

?>