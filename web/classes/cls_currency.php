<?php
   /**
    *  Where's My Money? – Currency Class
    * ====================================
    *  Created 2017-06-05
    */

    class Currency
    {
        // Privates
        private $db;

        // Constructor
        function __construct($oDB) {
            $this->db = $oDB;
        }

        // Wrappers
        private function dbWrap($dbVar,$varType="text") {

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
            }
            return $dbVar;
        }

        // Methods

       /**
        *  Get data from currency table
        * ==============================
        *  - Pulls a single record if ID is specified, otherwise pulls all
        */

        public function getData($ID=0) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "Currency",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT ";
            $SQL .= "ID, Country, Name, ISO, Symbol, Type, Factor ";
            $SQL .= "FROM currency ";
            $SQL .= "WHERE ID > 0 ";
            if($ID > 0) {
                $SQL .= "AND ID = '".$this->db->real_escape_string($ID)."'";
            }
            $SQL .= "ORDER BY ISO ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            }

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = array(
                    "ID"      => $aRow["ID"],
                    "Country" => $aRow["Country"],
                    "Name"    => $aRow["Name"],
                    "ISO"     => $aRow["ISO"],
                    "Symbol"  => $aRow["Symbol"],
                    "Type"    => $aRow["Type"],
                    "Factor"  => $aRow["Factor"],
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

            return $aReturn;
        }

        public function saveData($aData) {

            $SQL = "";
            foreach($aData as $iKey=>$aRow) {

                if(array_key_exists("ID",$aRow)) {
                    $SQL .= "UPDATE currency SET ";
                    $SQL .= "Country = "     .$this->dbWrap($aRow["Country"],"text").", ";
                    $SQL .= "Name = "        .$this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= "ISO = "         .$this->dbWrap($aRow["ISO"],"text").", ";
                    $SQL .= "Symbol = "      .$this->dbWrap($aRow["Symbol"],"text").", ";
                    $SQL .= "Type = "        .$this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= "Factor = "      .$this->dbWrap($aRow["Factor"],"int")." ";
                    $SQL .= "WHERE ID = "    .$this->dbWrap($aRow["ID"],"int").";\n";
                } else {
                    $SQL .= "INSERT INTO transactions (";
                    $SQL .= "Country, ";
                    $SQL .= "Name, ";
                    $SQL .= "ISO, ";
                    $SQL .= "Symbol, ";
                    $SQL .= "Type, ";
                    $SQL .= "Facro ";
                    $SQL .= ") VALUES (";
                    $SQL .= $this->dbWrap($aRow["Country"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["ISO"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Symbol"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Factor"],"int").");\n";
                }
            }
            if($SQL == "") return true;

            $oRes = $this->db->multi_query($SQL);
            while($this->db->more_results()) $this->db->next_result();

            if(!$oRes) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            } else {
                return true;
            }
        }
    }
?>
