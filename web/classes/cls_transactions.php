<?php
   /**
    *  Where's My Money? – Transactions Class
    * ========================================
    *  Created 2017-06-01
    */

    class Transact
    {
        // Publics

        // Privates
        private $db;
        private $fundsID  = null;
        private $fromDate = null;
        private $toDate   = null;


        // Constructor
        function __construct($oDB) {
            $this->db = $oDB;
        }

        // Methods
        public function setFilter($filterType, $filterValue) {
            switch($filterType) {
                case "FundsID":
                    $this->fundsID  = $filterValue;
                    break;
                case "FromDate":
                    $this->fromDate = $filterValue;
                    break;
                case "ToDate":
                    $this->toDate   = $filterValue;
                    break;
                default:
                    echo "Unknown filter type ...<br />";
                    break;
            }
        }

        public function unsetFilter($filterType) {
            switch($filterType) {
                case "FundsID":
                    $this->fundsID  = null;
                    break;
                case "FromDate":
                    $this->fromDate = null;
                    break;
                case "ToDate":
                    $this->toDate   = null;
                    break;
            }
        }

        public function getEntry($ID=0) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "Transactions",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT ";
            $SQL .= "t.ID AS ID, ";
            $SQL .= "f.Name AS FundsName, ";
            $SQL .= "t.RecordDate AS RecordDate, ";
            $SQL .= "t.TransactionDate AS TransactionDate, ";
            $SQL .= "t.Details AS Details, ";
            $SQL .= "t.Original AS Original, ";
            $SQL .= "tc.ISO AS Currency, ";
            $SQL .= "tc.Factor AS CurrencyFac, ";
            $SQL .= "t.Amount AS Amount, ";
            $SQL .= "t.Complete AS Complete, ";
            $SQL .= "fc.Factor AS AmountFac ";
            $SQL .= "FROM transactions AS t ";
            $SQL .= "LEFT JOIN funds AS f ON f.ID = t.FundsID ";
            $SQL .= "LEFT JOIN currency AS tc ON tc.ID = t.CurrencyID ";
            $SQL .= "LEFT JOIN currency AS fc ON fc.ID = f.CurrencyID ";
            if($ID > 0) {
                $SQL .= "WHERE t.ID = '".$this->db->real_escape_string($ID)."' ";
            } else {
                $SQL .= "WHERE t.ID > 0 ";
            }
            if(!is_null($this->fundsID)) {
                $SQL .= "AND t.FundsID = '".$this->db->real_escape_string($this->fundsID)."' ";
            }
            if(!is_null($this->fromDate)) {
                $SQL .= "AND t.RecordDate >= '".date("Y-m-d",$this->fromDate)."' ";
            }
            if(!is_null($this->toDate)) {
                $SQL .= "AND t.RecordDate <= '".date("Y-m-d",$this->toDate)."' ";
            }
            $oData = $this->db->query($SQL);

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = $aRow;
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

            return $aReturn;
        }

        public function saveTemp($aData, $removeOld=false) {

            if(is_null($this->fundsID)) return false;

            if($removeOld) {
                $SQL = "DELETE FROM transactions_temp WHERE FundsID = '".$this->db->real_escape_string($this->fundsID)."';\n";
            } else {
                $SQL = "";
            }

            reset($aData);
            foreach($aData as $iKey=>$aRow) {
                if(array_key_exists("ID",$aRow)) {
                    $SQL .= "UPDATE transactionss_temp SET ";
                    $SQL .= "FundsID = '".$this->db->real_escape_string($this->fundsID)."', ";
                    $SQL .= "RecordDate = '".date("Y-m-d",$aRow["RecordDate"])."', ";
                    $SQL .= "TransactionDate = '".date("Y-m-d",$aRow["TransactionDate"])."', ";
                    $SQL .= "Details = '".$this->db->real_escape_string($aRow["Details"])."', ";
                    $SQL .= "Original = '".$this->db->real_escape_string($aRow["Original"])."', ";
                    $SQL .= "Currency = '".$this->db->real_escape_string($aRow["Currency"])."', ";
                    $SQL .= "Amount = '".$this->db->real_escape_string($aRow["Amount"])."', ";
                    $SQL .= "Hash = '".$this->db->real_escape_string($aRow["Hash"])."' ";
                    $SQL .= "WHERE ID = '".$this->db->real_escape_string($aRow["ID"])."' ";
                    $SQL .= "AND FundsID = '".$this->fundsID."';\n";
                } else {
                    $SQL .= "INSERT INTO transactions_temp (";
                    $SQL .= "FundsID, ";
                    $SQL .= "RecordDate, ";
                    $SQL .= "TransactionDate, ";
                    $SQL .= "Details, ";
                    $SQL .= "Original, ";
                    $SQL .= "Currency, ";
                    $SQL .= "Amount, ";
                    $SQL .= "Hash ";
                    $SQL .= ") VALUES (";
                    $SQL .= "'".$this->db->real_escape_string($this->fundsID)."', ";
                    $SQL .= "'".date("Y-m-d",$aRow["RecordDate"])."', ";
                    $SQL .= "'".date("Y-m-d",$aRow["TransactionDate"])."', ";
                    $SQL .= "'".$this->db->real_escape_string($aRow["Details"])."', ";
                    $SQL .= "'".$this->db->real_escape_string($aRow["Original"])."', ";
                    $SQL .= "'".$this->db->real_escape_string($aRow["Currency"])."', ";
                    $SQL .= "'".$this->db->real_escape_string($aRow["Amount"])."', ";
                    $SQL .= "'".$this->db->real_escape_string($aRow["Hash"])."');\n";
                }
            }
            $oRes = $this->db->multi_query($SQL);

            if($oRes === false) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                exit();
            } else {
                return true;
            }
        }
    }
?>
