<?php
   /**
    *  Where's My Money? – Transactions Class
    * ========================================
    *  Created 2017-06-01
    */

    class Transact extends DataBase
    {
        // Privates
        private $fundsID  = null;
        private $fromDate = null;
        private $toDate   = null;
        private $pageSize = 50;
        private $pageNum  = 1;

        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
        }

        // Methods
        public function setFilter($filterType, $filterValue) {
            switch($filterType) {
                case "FundsID":
                    $this->fundsID  = intval($filterValue);
                    break;
                case "FromDate":
                    $this->fromDate = intval($filterValue);
                    break;
                case "ToDate":
                    $this->toDate   = intval($filterValue);
                    break;
                case "PageSize":
                    $this->pageSize = intval($filterValue);
                    break;
                case "PageNum":
                    $this->pageNum  = intval($filterValue);
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
                case "PageSize":
                    $this->pageSize = 50;
                    break;
                case "PageNum":
                    $this->pageNum  = 1;
                    break;
                default:
                    echo "Unknown filter type ...<br />";
                    break;
            }
        }

        public function getCount() {

            if(is_null($this->fundsID)) return false;

            $SQL  = "SELECT COUNT(ID) AS Records FROM transactions ";
            $SQL .= "WHERE FundsID = ".$this->dbWrap($this->fundsID,"int")." ";
            if(!is_null($this->fromDate)) {
                $SQL .= "AND RecordDate >= '".date("Y-m-d",$this->fromDate)."' ";
            }
            if(!is_null($this->toDate)) {
                $SQL .= "AND RecordDate <= '".date("Y-m-d",$this->toDate)."' ";
            }
            $oData = $this->db->query($SQL);
            $aRow  = $oData->fetch_assoc();

            return $aRow["Records"];
        }

       /**
        *  Get data from transactions table
        * ==================================
        *  - Pulls a single record if ID is specified, otherwise pulls all that matches the filters
        *    set by the setFilter method
        */

        public function getData($ID=0, $splitPages=false) {

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
            $SQL .= "t.CurrencyID AS CurrencyID, ";
            $SQL .= "tc.Factor AS CurrencyFac, ";
            $SQL .= "t.Amount AS Amount, ";
            $SQL .= "fc.Factor AS AmountFac, ";
            $SQL .= "cm.Height AS BlockHeight, ";
            $SQL .= "cm.Hash AS TransactionHash, ";
            $SQL .= "IF(a.Count IS NULL, 0, a.Count) AS AccCount, ";
            $SQL .= "IF(a.Total IS NULL, 0, a.Total) AS AccTotal ";
            $SQL .= "FROM transactions AS t ";
            $SQL .= "LEFT JOIN funds AS f ON f.ID = t.FundsID ";
            $SQL .= "LEFT JOIN currency AS tc ON tc.ID = t.CurrencyID ";
            $SQL .= "LEFT JOIN currency AS fc ON fc.ID = f.CurrencyID ";
            $SQL .= "LEFT JOIN crypto_meta AS cm ON cm.transactionID = t.ID ";
            $SQL .= "LEFT JOIN (";
            $SQL .=     "SELECT TransactionID, ";
            $SQL .=     "COUNT(ID) AS Count, ";
            $SQL .=     "SUM(Amount) AS Total ";
            $SQL .=     "FROM accounting GROUP BY TransactionID";
            $SQL .= ") AS a ON t.ID = a.TransactionID ";
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
            $SQL .= "ORDER BY t.RecordDate DESC, t.ID DESC ";
            if($splitPages) {
                $SQL .= "LIMIT ".(($this->pageNum-1)*$this->pageSize).",".$this->pageSize." ";
            }
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
                    "ID"              => $aRow["ID"],
                    "FundsName"       => $aRow["FundsName"],
                    "RecordDate"      => strtotime($aRow["RecordDate"]),
                    "TransactionDate" => $aRow["TransactionDate"] === null ? null : strtotime($aRow["TransactionDate"]),
                    "Details"         => $aRow["Details"],
                    "Original"        => $aRow["Original"],
                    "Currency"        => $aRow["Currency"],
                    "CurrencyID"      => $aRow["CurrencyID"],
                    "CurrencyFac"     => $aRow["CurrencyFac"],
                    "Amount"          => $aRow["Amount"],
                    "AmountFac"       => $aRow["AmountFac"],
                    "AccCount"        => $aRow["AccCount"],
                    "AccTotal"        => $aRow["AccTotal"],
                    "BlockHeight"     => $aRow["BlockHeight"],
                    "TransactionHash" => $aRow["TransactionHash"],
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

       /**
        *  Save data to transactions table
        * =================================
        *  - Requirers fundsID to be set.
        *  - Takes an array of arrau records as input and saves them or updates the corresponding
        *    value if an ID is specified.
        */

        public function saveData($aData) {

            if(is_null($this->fundsID)) return false;

            // Get currencies
            $oCurrencies = $this->db->query("SELECT ID, ISO FROM currency");
            $aCurrencyIDs = array();
            while($aCurrency = $oCurrencies->fetch_assoc()) {
                $aCurrencyIDs[$aCurrency["ISO"]] = $aCurrency["ID"];
            }

            $SQL = "";
            foreach($aData as $iKey=>$aRow) {

                if(!array_key_exists("CurrencyID", $aRow)) {
                    if(array_key_exists($aRow["Currency"],$aCurrencyIDs)) {
                        $aRow["CurrencyID"] = $aCurrencyIDs[$aRow["Currency"]];
                    } else {
                        $aRow["CurrencyID"] = null;
                    }
                }

                $updateID = array_key_exists("ID",$aRow) ? $aRow["ID"] : 0;

                if($updateID > 0) {
                    $SQL .= "UPDATE transactions SET ";
                    $SQL .= "FundsID = "        .$this->dbWrap($this->fundsID,"int").", ";
                    $SQL .= "RecordDate = "     .$this->dbWrap($aRow["RecordDate"],"date").", ";
                    $SQL .= "TransactionDate = ".$this->dbWrap($aRow["TransactionDate"],"date").", ";
                    $SQL .= "Details = "        .$this->dbWrap($aRow["Details"],"text").", ";
                    $SQL .= "Original = "       .$this->dbWrap($aRow["Original"],"int").", ";
                    $SQL .= "CurrencyID = "     .$this->dbWrap($aRow["CurrencyID"],"int").", ";
                    $SQL .= "Amount = "         .$this->dbWrap($aRow["Amount"],"int")." ";
                    $SQL .= "WHERE ID = "       .$this->dbWrap($aRow["ID"],"int")." ";
                    $SQL .= "AND FundsID = "    .$this->dbWrap($this->fundsID,"int").";\n";
                    if(array_key_exists("BlockHeight", $aRow) || array_key_exists("TransactionHash", $aRow)) {
                        if(!is_null($aRow["BlockHeight"]) || !is_null($aRow["TransactionHash"])) {
                            $SQL .= "UPDATE crypto_meta SET ";
                            $SQL .= "Height = "             .$this->dbWrap($aRow["BlockHeight"],"int").", ";
                            $SQL .= "Hash = "               .$this->dbWrap($aRow["TransactionHash"],"text")." ";
                            $SQL .= "WHERE TransactionID = ".$this->dbWrap($aRow["ID"],"int").";\n";
                        }
                    }
                } else {
                    $SQL .= "INSERT INTO transactions (";
                    $SQL .= "FundsID, ";
                    $SQL .= "RecordDate, ";
                    $SQL .= "TransactionDate, ";
                    $SQL .= "Details, ";
                    $SQL .= "Original, ";
                    $SQL .= "CurrencyID, ";
                    $SQL .= "Amount, ";
                    $SQL .= "Created ";
                    $SQL .= ") VALUES (";
                    $SQL .= $this->dbWrap($this->fundsID,"int").", ";
                    $SQL .= $this->dbWrap($aRow["RecordDate"],"date").", ";
                    $SQL .= $this->dbWrap($aRow["TransactionDate"],"date").", ";
                    $SQL .= $this->dbWrap($aRow["Details"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Original"],"int").", ";
                    $SQL .= $this->dbWrap($aRow["CurrencyID"],"int").", ";
                    $SQL .= $this->dbWrap($aRow["Amount"],"int").", ";
                    $SQL .= $this->dbWrap(time(),"datetime").");\n";
                    if(array_key_exists("BlockHeight", $aRow) || array_key_exists("TransactionHash", $aRow)) {
                        $SQL .= "SELECT LAST_INSERT_ID() INTO @TransactionID;\n";
                        $SQL .= "INSERT INTO crypto_meta (";
                        $SQL .= "TransactionID, ";
                        $SQL .= "Height, ";
                        $SQL .= "Hash ";
                        $SQL .= ") VALUES (";
                        $SQL .= "@TransactionID, ";
                        $SQL .= $this->dbWrap($aRow["BlockHeight"],"int").", ";
                        $SQL .= $this->dbWrap($aRow["TransactionHash"],"text").");\n";
                    }
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
        *  Delete data in transactions table
        * ===================================
        *  - Requires a fundsID to be set.
        *  - Takes an array of IDs as input.
        */

        public function deleteData($aIDs) {

            if(is_null($this->fundsID)) return false; // Safety requirement
            if(count($aIDs) == 0) return true;        // Nothing to delete

            $SQL = "";
            foreach($aIDs as $iID) {
                if(!$iID > 0) continue;
                $SQL .= "DELETE FROM transactions ";
                $SQL .= "WHERE FundsID = ".$this->dbWrap($this->fundsID,"int")." ";
                $SQL .= "AND ID = ".$this->dbWrap($iID,"int").";\n";
            }

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
        *  Get a list of last used details entries
        * =========================================
        *  - Requires a fundsID to be set
        */

        public function getLastDetails($nCount=10) {

            if(is_null($this->fundsID)) return false;

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "LastDetauls",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT DISTINCT Details ";
            $SQL .= "FROM transactions ";
            $SQL .= "WHERE FundsID = ".$this->dbWrap($this->fundsID,"int")." ";
            $SQL .= "ORDER BY ID DESC ";
            $SQL .= "LIMIT 0,".$nCount;
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
                    "Details" => $aRow["Details"],
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

        public function getYearlyStatus($getYear="") {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "YearlyStatus",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            if($getYear == "") {
                $SQL  = "SELECT ";
                $SQL .= "YEAR(t.RecordDate) AS Year, ";
                $SQL .= "COUNT(DISTINCT FundsID) AS Funds, ";
                $SQL .= "ty.Count AS Count, ";
                $SQL .= "ty.Locked AS Locked ";
                $SQL .= "FROM transactions AS t ";
                $SQL .= "LEFT JOIN (";
                $SQL .=     "SELECT ";
                $SQL .=     "RecordDate, ";
                $SQL .=     "COUNT(ID) AS Count, ";
                $SQL .=     "SUM(Locked IS NOT NULL) AS Locked ";
                $SQL .=     "FROM transactions_yearly";
                $SQL .= ") AS ty ON YEAR(ty.RecordDate) = YEAR(t.RecordDate) ";
                $SQL .= "GROUP BY Year";
            } else {
                $SQL  = "SELECT ";
                $SQL .= "YEAR(t.RecordDate) AS Year, ";
                $SQL .= "f.Name AS FundsName, ";
                $SQL .= "f.ID AS ID, ";
                $SQL .= "COUNT(f.ID) AS Count, ";
                $SQL .= "c.ISO AS Currency, ";
                $SQL .= "c.Factor AS Factor, ";
                $SQL .= "SUM(t.Amount) AS Balance, ";
                $SQL .= "ty.Amount AS Summed, ";
                $SQL .= "ty.Locked IS NOT NULL AS Locked ";
                $SQL .= "FROM transactions AS t ";
                $SQL .= "LEFT JOIN transactions_yearly AS ty ";
                $SQL .=     "ON YEAR(ty.RecordDate) = YEAR(t.RecordDate) ";
                $SQL .=     "AND ty.FundsID = t.FundsID ";
                $SQL .= "LEFT JOIN funds AS f ON f.ID = t.FundsID ";
                $SQL .= "LEFT JOIN currency AS c ON c.ID = f.CurrencyID ";
                $SQL .= "WHERE YEAR(t.RecordDate) = ".$this->dbWrap($getYear,"int")." ";
                $SQL .= "GROUP BY t.FundsID ";
                $SQL .= "ORDER BY FIELD(f.Type,'B','C','X'), f.BankID ASC, FIELD(f.Category,'P','S','C'), f.ID ASC ";
            }
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            }

            while($aRow = $oData->fetch_assoc()) {
                if($getYear == "") {
                    $aReturn["Data"][] = array(
                        "Year"   => $aRow["Year"],
                        "Funds"  => $aRow["Funds"],
                        "Count"  => $aRow["Count"],
                        "Locked" => $aRow["Locked"],
                    );
                } else {
                    $aReturn["Data"][] = array(
                        "ID"        => $aRow["ID"],
                        "Year"      => $aRow["Year"],
                        "FundsName" => $aRow["FundsName"],
                        "Count"     => $aRow["Count"],
                        "Currency"  => $aRow["Currency"],
                        "Factor"    => $aRow["Factor"],
                        "Balance"   => $aRow["Balance"],
                        "Summed"    => $aRow["Summed"],
                        "Locked"    => $aRow["Locked"],
                    );
                }
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

        public function calcYear($doYear, $fundsID) {

            if($doYear == "" || $fundsID == 0) return false;

            $SQL  = "SELECT ";
            $SQL .= "SUM(Amount) INTO @TransactionsSum ";
            $SQL .= "FROM transactions ";
            $SQL .= "WHERE FundsID = ".$this->dbWrap($fundsID,"int")." ";
            $SQL .= "AND YEAR(RecordDate) = ".$this->dbWrap($doYear,"text").";\n";
            $SQL .= "INSERT INTO transactions_yearly (";
            $SQL .= "FundsID, RecordDate, Amount, Updated";
            $SQL .= ") VALUES (";
            $SQL .= $this->dbWrap($fundsID,"int").", ";
            $SQL .= $this->dbWrap($doYear."-12-31","text").", ";
            $SQL .= "@TransactionsSum, ";
            $SQL .= $this->dbWrap(time(),"datetime").") ";
            $SQL .= "ON DUPLICATE KEY UPDATE ";
            $SQL .= "Amount = VALUES(Amount), ";
            $SQL .= "Updated = VALUES(Updated);\n";
            echo "The Query was:<br />";
            echo str_replace("\n","<br />",$SQL);

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
