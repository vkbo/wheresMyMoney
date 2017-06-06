<?php
   /**
    *  Where's My Money? – Settings Class
    * ====================================
    *  Created 2017-06-06
    */

    class Settings extends DataBase
    {
        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
        }

        public function getValue($getName, $defValue="") {
            $SQL   = "SELECT Value FROM settings WHERE Name = ".$this->dbWrap($getName,"text");
            $oData = $this->db->query($SQL);
            if($oData === false) {
                return $defValue;
            } else {
                $aRow = $oData->fetch_assoc();
                return $aRow["Value"];
            }
        }

        public function setValue($setName, $setValue) {
            $SQL  = "INSERT INTO settings (";
            $SQL .= "Name, Value, Altered";
            $SQL .= ") VALUES (";
            $SQL .= $this->dbWrap($setName,"text").", ";
            $SQL .= $this->dbWrap($setValue,"text").", ";
            $SQL .= $this->dbWrap(time(),"datetime").") ";
            $SQL .= "ON DUPLICATE KEY UPDATE ";
            $SQL .= "Name = VALUES(Name), ";
            $SQL .= "Value = VALUES(Value), ";
            $SQL .= "Altered = VALUES(Altered)";
            $oRes = $this->db->query($SQL);
            if($oRes === false) {
                return false;
            } else {
                return true;
            }
        }

        public function unsetValue($unsetName) {
            $SQL  = "DELETE FROM settings WHERE Name = ".$this->dbWrap($unsetName,"text");
            $oRes = $this->db->query($SQL);
            if($oRes === false) {
                return false;
            } else {
                return true;
            }
        }
    }
?>
