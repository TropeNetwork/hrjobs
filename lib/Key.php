<?php
class Key {

    
    /**
    * validate OHRwurm key
    **/
    public static function checkKey($key){
        $client = new SoapClient($GLOBALS['settings']['ohrwurm']['wsdl'],array('trace' =>1));
        try {
            $return = $client->checkKey($key);
        } catch (SoapFault $fault) {
            trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_ERROR);
        } 
        return $return;
    }
}

?>