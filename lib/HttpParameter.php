<?php

class HttpParameter {

    public static function getParameter($name, $metod = 'ALL') {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }        
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return null;
    }
    
    
}

?>