<?php
   /**
    *  Where's My Money? – Funds Class
    * =================================
    *  Created 2017-05-31
    */

    class Funds extends DataBase
    {
        // Variables
        private $year;

        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
            $this->year = date("Y",time());
        }

        // Methods
        public function setFilter($filterType, $filterValue) {
            switch($filterType) {
            case "Year":
                $this->year = intval($filterValue);
                break;
            default:
                echo "Unknown filter type ...<br />";
                break;
            }
        }

        public function unsetFilter($filterType) {
            switch($filterType) {
            case "Year":
                $this->year = date("Y",time());
                break;
            default:
                echo "Unknown filter type ...<br />";
                break;
            }
        }

       /**
        *  Get data from funds table
        * ===========================
        *  - Pulls a single record if ID is specified, otherwise pulls all
        */

        public function getData($ID=0) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "Funds",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT ";
            $SQL .= "f.ID AS ID, ";
            $SQL .= "f.Name AS FundsName, ";
            $SQL .= "f.AccountNumber AS AccountNumber, ";
            $SQL .= "f.SwiftIBAN AS SwiftIBAN, ";
            $SQL .= "f.Type AS Type, ";
            $SQL .= "f.Category AS Category, ";
            $SQL .= "f.BankID AS BankID, ";
            $SQL .= "f.CurrencyID AS CurrencyID, ";
            $SQL .= "f.Opened AS Opened, ";
            $SQL .= "f.Closed AS Closed, ";
            $SQL .= "b.Name AS BankName, ";
            $SQL .= "c.ISO AS CurrencyISO, ";
            $SQL .= "c.Factor AS Factor, ";
            $SQL .= "t.Balance AS ThisYear, ";
            $SQL .= "ty.Balance AS StartOfYear ";
            $SQL .= "FROM funds AS f ";
            $SQL .= "LEFT JOIN bank AS b ON b.ID = f.BankID ";
            $SQL .= "LEFT JOIN currency AS c ON c.ID = f.CurrencyID ";
            $SQL .= "LEFT JOIN (";
            $SQL .=     "SELECT ";
            $SQL .=     "FundsID, ";
            $SQL .=     "SUM(Amount) AS Balance ";
            $SQL .=     "FROM transactions ";
            $SQL .=     "WHERE RecordDate >= '".$this->year."-01-01' ";
            $SQL .=     "AND RecordDate <= '".$this->year."-12-31' ";
            $SQL .=     "GROUP BY FundsID";
            $SQL .= ") AS t ON t.FundsID = f.ID ";
            $SQL .= "LEFT JOIN (";
            $SQL .=     "SELECT ";
            $SQL .=     "FundsID, ";
            $SQL .=     "SUM(Amount) AS Balance ";
            $SQL .=     "FROM transactions_yearly ";
            $SQL .=     "WHERE RecordDate <= '".($this->year-1)."-12-31' ";
            $SQL .=     "GROUP BY FundsID";
            $SQL .= ") AS ty ON ty.FundsID = f.ID ";
            $SQL .= "WHERE (Closed IS NULL OR Closed >= '".$this->year."-01-01') ";
            if($ID > 0) {
                $SQL .= "AND f.ID = '".$this->db->real_escape_string($ID)."' ";
            }
            $SQL .= "GROUP BY f.ID ";
            $SQL .= "ORDER BY FIELD(f.Type,'B','C','X'), b.ID ASC, FIELD(f.Category,'P','S','C'), f.ID ASC ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
            }

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = array(
                    "ID"            => $aRow["ID"],
                    "FundsName"     => $aRow["FundsName"],
                    "AccountNumber" => $aRow["AccountNumber"],
                    "SwiftIBAN"     => $aRow["SwiftIBAN"],
                    "Type"          => $aRow["Type"],
                    "Category"      => $aRow["Category"],
                    "BankID"        => $aRow["BankID"],
                    "CurrencyID"    => $aRow["CurrencyID"],
                    "Opened"        => strtotime($aRow["Opened"]),
                    "Closed"        => strtotime($aRow["Closed"]),
                    "BankName"      => $aRow["BankName"],
                    "CurrencyISO"   => $aRow["CurrencyISO"],
                    "Factor"        => $aRow["Factor"],
                    "ThisYear"      => $aRow["ThisYear"],
                    "StartOfYear"   => $aRow["StartOfYear"],
                    "Balance"       => $aRow["StartOfYear"]+$aRow["ThisYear"],
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

        public function saveData($aData) {

            $SQL = "";
            foreach($aData as $iKey=>$aRow) {

                $updateID = array_key_exists("ID",$aRow) ? $aRow["ID"] : 0;

                if($updateID > 0) {
                    $SQL .= "UPDATE funds SET ";
                    $SQL .= "Name = "         .$this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= "AccountNumber = ".$this->dbWrap($aRow["AccountNumber"],"text").", ";
                    $SQL .= "SwiftIBAN = "    .$this->dbWrap($aRow["SwiftIBAN"],"text").", ";
                    $SQL .= "Type = "         .$this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= "Category = "     .$this->dbWrap($aRow["Category"],"text").", ";
                    $SQL .= "BankID = "       .$this->dbWrap($aRow["BankID"],"int").", ";
                    $SQL .= "CurrencyID = "   .$this->dbWrap($aRow["CurrencyID"],"int").", ";
                    $SQL .= "Opened = "       .$this->dbWrap($aRow["Opened"],"date").", ";
                    $SQL .= "Closed = "       .$this->dbWrap($aRow["Closed"],"date")." ";
                    $SQL .= "WHERE ID = "     .$this->dbWrap($aRow["ID"],"int").";\n";
                } else {
                    $SQL .= "INSERT INTO funds (";
                    $SQL .= "Name, ";
                    $SQL .= "AccountNumber, ";
                    $SQL .= "SwiftIBAN, ";
                    $SQL .= "Type, ";
                    $SQL .= "Category, ";
                    $SQL .= "BankID, ";
                    $SQL .= "CurrencyID, ";
                    $SQL .= "Opened, ";
                    $SQL .= "Closed ";
                    $SQL .= ") VALUES (";
                    $SQL .= $this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["AccountNumber"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["SwiftIBAN"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Category"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["BankID"],"int").", ";
                    $SQL .= $this->dbWrap($aRow["CurrencyID"],"int").", ";
                    $SQL .= $this->dbWrap($aRow["Opened"],"date").", ";
                    $SQL .= $this->dbWrap($aRow["Closed"],"date").");\n";
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

       /**
        *  Return a list of all years in transaction table
        * =================================================
        */

        public function getYears() {

            $SQL   = "SELECT DISTINCT YEAR(RecordDate) AS Year FROM transactions";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            }

            $aReturn = array();
            $aRows   = $oData->fetch_all();;
            foreach($aRows as $aRow) {
                $aReturn[] = $aRow[0];
            }

            return $aReturn;
        }

        public function toAccount() {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "AccountingCount",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT ";
            $SQL .= "SUM((t.Amount - IF(a.Total IS NULL, 0, a.Total)) <> 0) AS ToDo ";
            $SQL .= "FROM transactions AS t ";
            $SQL .= "LEFT JOIN (";
            $SQL .=     "SELECT TransactionID, ";
            $SQL .=     "COUNT(ID) AS Count, ";
            $SQL .=     "SUM(Amount) AS Total ";
            $SQL .=     "FROM accounting GROUP BY TransactionID";
            $SQL .= ") AS a ON t.ID = a.TransactionID ";
            $SQL .= "WHERE t.Locked IS NULL ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            }

            $aReturn["Data"] = $oData->fetch_assoc();;

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }
    }
?>
