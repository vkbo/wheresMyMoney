<?php
   /**
    *  Where's My Money? – Funds Class
    * =================================
    *  Created 2017-05-31
    */

    class Funds
    {
        // Publics

        // Privates
        private $db;

        // Constructor
        function __construct($oDB) {
            $this->db = $oDB;
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
            $SQL .= "SUM(t.Amount) AS Balance ";
            $SQL .= "FROM funds AS f ";
            $SQL .= "LEFT JOIN bank AS b ON b.ID = f.BankID ";
            $SQL .= "LEFT JOIN currency AS c ON c.ID = f.CurrencyID ";
            $SQL .= "LEFT JOIN transactions AS t ON t.FundsID = f.ID ";
            if($ID > 0) {
                $SQL .= "WHERE f.ID = '".$this->db->real_escape_string($ID)."' ";
            }
            $SQL .= "GROUP BY f.ID ";
            $SQL .= "ORDER BY FIELD(f.Type,'B','C','X'), b.ID ASC, FIELD(f.Category,'P','S'), f.ID ASC ";
            $oData = $this->db->query($SQL);

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = $aRow;
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

            return $aReturn;
        }
    }
?>
