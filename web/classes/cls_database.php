<?php
   /**
    *  Where's My Money? – Database Class
    * ====================================
    *  Created 2017-06-06
    */

    class DataBase
    {
        // Protected Variables
        protected $db;

        // Constructor
        function __construct($oDB) {
            $this->db = $oDB;
        }

        // Wrappers
        protected function dbWrap($dbVar,$varType="text") {

            switch($varType) {
            case "text":
                $dbVar = $dbVar === null ? "NULL" : "'".$this->db->real_escape_string($dbVar)."'";
                break;
            case "int":
                $dbVar = $dbVar === null ? "NULL" : intval($dbVar);
                break;
            case "float":
                $dbVar = $dbVar === null ? "NULL" : floatval($dbVar);
                break;
            case "date":
                $dbVar = $dbVar === null ? "NULL" : date("'Y-m-d'",$dbVar);
                break;
            case "datetime":
                $dbVar = $dbVar === null ? "NULL" : date("'Y-m-d H:i:s'",$dbVar);
                break;
            case "bool":
                $dbVar = $dbVar === null ? "NULL" : ($dbVar ? "1" : "0");
                break;
            }
            return $dbVar;
        }
    }
?>
