<?php

class Date {
    
    static function sqlDate($date = array()) {
        return $date['Y'].'-'.$date['F'].'-'.$date['d'];    
    }
    
    static function quickDate($date = array()) {
        return strtotime(self::sqlDate($date));    
    }
    
    static function quickFormat() {
        return 'dFY';
    }
    
    static function minYear() {
        return date(Y);
    }
    
    static function getDynDateJS($name) {
        $js = "\n<script language=\"JavaScript\" type=\"text/javascript\">".
               "\n<!--\n function set_value_$name(date, month, year)".
               "\n{".
               "\n\tdocument.forms[0].elements['".$name."[d]'].value = date;".
               "\n\tdocument.forms[0].elements['".$name."[F]'].value = month;".
               "\n\tdocument.forms[0].elements['".$name."[Y]'].value = year;".
               "\n}".
               "\ncalendar_$name = new dynCalendar('calendar_$name', 'set_value_$name', '/images/' );".
               "\n//-->".
               "\n</script>";
         return $js;
    }
}

?>