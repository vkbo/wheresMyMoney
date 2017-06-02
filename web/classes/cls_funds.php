<?php
   /**
    *  Where's My Money? – Funds Class
    * =================================
    *  Created 2017-05-31
    */

    class Funds
    {
        // Variables
        private $db;
        private $year;

        // Constructor
        function __construct($oDB) {
            $this->db   = $oDB;
            $this->year = date("Y",time());
        }

        // Methods
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
            $SQL .= "f.Type AS Type, ";
            $SQL .= "f.Category AS Category, ";
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
            $SQL .= "ORDER BY FIELD(f.Type,'B','C','X'), b.ID ASC, FIELD(f.Category,'P','S'), f.ID ASC ";
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
                    "Type"          => $aRow["Type"],
                    "Category"      => $aRow["Category"],
                    "Opened"        => $aRow["Opened"],
                    "Closed"        => $aRow["Closed"],
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
            $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

            return $aReturn;
        }
    }
?>
