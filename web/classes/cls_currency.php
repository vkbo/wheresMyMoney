<?php
   /**
    *  Where's My Money? – Currency Class
    * ====================================
    *  Created 2017-06-05
    */

    class Currency extends DataBase
    {
        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
        }

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
            $SQL .= "ID, Country, Name, ISO, Symbol, Type, Factor, RefCurrency ";
            $SQL .= "FROM currency ";
            $SQL .= "WHERE ID > 0 ";
            if($ID > 0) {
                $SQL .= "AND ID = '".$this->db->real_escape_string($ID)."'";
            }
            $SQL .= "ORDER BY Type, ISO ";
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
                    "ID"          => $aRow["ID"],
                    "Country"     => $aRow["Country"],
                    "Name"        => $aRow["Name"],
                    "ISO"         => $aRow["ISO"],
                    "Symbol"      => $aRow["Symbol"],
                    "Type"        => $aRow["Type"],
                    "Factor"      => $aRow["Factor"],
                    "RefCurrency" => $aRow["RefCurrency"] == 1,
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

       /**
        *  Saves data to currency table
        * ==============================
        */

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

       /**
        *  Retrieve exchange rate for a given base currency for a given date
        * ===================================================================
        */

        public function getXRates($dDate,$xBase) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content"  => "XRates",
                    "Count"    => 0,
                ),
                "Base" => array(
                    "ISO"    => $xBase,
                    "Date"   => 0,
                    "Factor" => 1,
                ),
                "Data" => array(),
            );

            $aFiat  = $this->getFiat($dDate);
            $aCrypt = $this->getCrypto($dDate);

            if($xBase == "EUR") {
                $baseRate = 1.0;
                $baseDate = strtotime(date("Y-m-d",time()));
                $baseFac  = 100;
            } elseif(array_key_exists($xBase, $aFiat["Data"])) {
                $baseRate = $aFiat["Data"][$xBase]["Rate"];
                $baseDate = $aFiat["Data"][$xBase]["RateDate"];
                $baseFac  = $aFiat["Data"][$xBase]["Factor"];
            } elseif(array_key_exists($xBase, $aCrypt["Data"])) {
                $baseRate = $aCrypt["Data"][$xBase]["Rate"];
                $baseDate = $aCrypt["Data"][$xBase]["RateDate"];
                $baseFac  = $aCrypt["Data"][$xBase]["Factor"];
            } else {
                $baseRate = 0.0;
                $baseDate = 0;
                $baseFac  = 1;
            }

            foreach($aFiat["Data"] as $sISO=>$aRate) {
                $aReturn["Data"][$sISO]["Rate"]     = $baseRate == 0 ? 0 : $aRate["Rate"]/$baseRate;
                $aReturn["Data"][$sISO]["RateDate"] = $aRate["RateDate"];
                $aReturn["Data"][$sISO]["Factor"]   = $baseFac/$aRate["Factor"];
            }
            foreach($aCrypt["Data"] as $sISO=>$aRate) {
                $aReturn["Data"][$sISO]["Rate"]     = $baseRate == 0 ? 0 : $aRate["Rate"]/$baseRate;
                $aReturn["Data"][$sISO]["RateDate"] = $aRate["RateDate"];
                $aReturn["Data"][$sISO]["Factor"]   = $baseFac/$aRate["Factor"];
            }

            $aReturn["Meta"]["Count"]  = count($aReturn["Data"]);
            $aReturn["Base"]["Date"]   = $baseDate;
            $aReturn["Base"]["Factor"] = $baseFac;
            $aReturn["Meta"]["Pull"]   = $aFiat["Meta"]["Pull"] || $aCrypt["Meta"]["Pull"] ;

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

       /**
        *  Private Functions
        * ===================
        */

        // Pull latest exchange rates from cache in database
        private function requestXRates($dDate) {

            $SQL  = "SELECT ";
            $SQL .= "c.ID AS ID, ";
            $SQL .= "c.ISO AS ISO, ";
            $SQL .= "c.Type AS Type, ";
            $SQL .= "c.Factor AS Factor, ";
            $SQL .= "ee.Date AS Date, ";
            $SQL .= "ee.Rate AS Rate, ";
            $SQL .= "ee.RateDate AS RateDate ";
            $SQL .= "FROM currency AS c ";
            $SQL .= "LEFT JOIN (";
            $SQL .=     "SELECT MAX(Date) AS Latest, CurrencyID ";
            $SQL .=     "FROM euro_exchange ";
            $SQL .=     "WHERE Date <= ".$this->dbWrap($dDate,"date")." ";
            $SQL .=     "GROUP BY CurrencyID";
            $SQL .= ") AS tmp ON tmp.CurrencyID = c.ID ";
            $SQL .= "LEFT JOIN euro_exchange AS ee ON ee.Date = tmp.Latest AND ee.CurrencyID = c.ID ";
            $SQL .= "ORDER BY c.Type ASC, c.ISO ASC ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            }

            $aReturn = array();
            while($aRow = $oData->fetch_assoc()) {
                $aReturn[$aRow["ISO"]] = array(
                    "ID"       => $aRow["ID"],
                    "Type"     => $aRow["Type"],
                    "Factor"   => $aRow["Factor"],
                    "Date"     => strtotime($aRow["Date"]),
                    "Rate"     => $aRow["Rate"],
                    "RateDate" => strtotime($aRow["RateDate"]),
                );
            }

            return $aReturn;
        }

        // Extract fiat rates. If not in cache, pull from API
        private function getFiat($dDate) {

            // Exchange rates pulled from http://fixer.io/

            // API is updated at 16:00. Setting delay to 16:30.
            // Requests for exchange rates before 16:30 on the current date will look up
            // yesterday's rates instead.
            $iDelay  = 16*3600+1500;
            $dDate   = time()-$dDate < $iDelay ? $dDate-$iDelay : $dDate;
            $dDate   = strtotime(date("Y-m-d",$dDate));

            $bPull   = false;
            $aReturn = array("Meta" => array("Pull" => false));
            $aRates  = $this->requestXRates($dDate);
            foreach($aRates as $sISO=>$aRate) {
                if($aRate["Type"] != "F") continue;
                $aReturn["Data"][$sISO] = $aRate;
                if($sISO == "EUR") {
                    $aReturn["Data"][$sISO]["Date"]     = $dDate;
                    $aReturn["Data"][$sISO]["Rate"]     = 1.0;
                    $aReturn["Data"][$sISO]["RateDate"] = $dDate;
                    continue;
                }
                if($aRate["Date"] != $dDate) $bPull = true;
            }

            if($bPull) {
                $sAPI  = "http://api.fixer.io/latest?base=EUR&date=".date("Y-m-d",$dDate);
                $aJson = $this->getJsonData($sAPI);
                $SQL   = "";
                foreach($aRates as $sISO=>$aRate) {
                    if($aRate["Type"] != "F") continue;
                    if($sISO == "EUR") continue;
                    if(array_key_exists($sISO, $aJson["rates"])) {
                        $SQL .= "INSERT INTO euro_exchange (";
                        $SQL .= "Date, CurrencyID, Rate, RateDate, Acquired";
                        $SQL .= ") VALUES (";
                        $SQL .= $this->dbWrap($dDate,"date").", ";
                        $SQL .= $this->dbWrap($aRate["ID"],"int").", ";
                        $SQL .= $this->dbWrap($aJson["rates"][$sISO],"float").", ";
                        $SQL .= $this->dbWrap($aJson["date"],"text").", ";
                        $SQL .= $this->dbWrap(time(),"datetime").") ";
                        $SQL .= "ON DUPLICATE KEY UPDATE ";
                        $SQL .= "Date = VALUES(Date), ";
                        $SQL .= "CurrencyID = VALUES(CurrencyID), ";
                        $SQL .= "Rate = VALUES(Rate), ";
                        $SQL .= "RateDate = VALUES(RateDate), ";
                        $SQL .= "Acquired = VALUES(Acquired);\n";
                    }
                    $aReturn["Data"][$sISO]["Date"]     = $dDate;
                    $aReturn["Data"][$sISO]["Rate"]     = $aJson["rates"][$sISO];
                    $aReturn["Data"][$sISO]["RateDate"] = strtotime($aJson["date"]);
                }
                if($SQL != "") {
                    $oRes = $this->db->multi_query($SQL);
                    while($this->db->more_results()) $this->db->next_result();

                    if(!$oRes) {
                        echo "MySQL Query Failed ...<br />";
                        echo "Error: ".$this->db->error."<br />";
                        echo "The Query was:<br />";
                        echo str_replace("\n","<br />",$SQL);
                    }
                }
            }
            $aReturn["Meta"]["Pull"] = $bPull;

            return $aReturn;
        }

        // Extract crypto rates. If not in cache, pull from API
        private function getCrypto($dDate) {

            // Exchange rates pulled from https://www.cryptocompare.com

            // The API call used delivers the coin price at the end of the day.
            // Therefore this function will only look up values that are from at least the
            // day before. Snapshot pricing is also available with a different call, and could be
            // implemented in the future.
            $iDelay  = 86400;
            $dDate   = time()-$dDate < $iDelay ? $dDate-$iDelay : $dDate;
            $dDate   = strtotime(date("Y-m-d",$dDate));

            $bPull   = false;
            $aReturn = array("Meta" => array("Pull" => false));
            $aRates  = $this->requestXRates($dDate);
            $sPull   = "";
            foreach($aRates as $sISO=>$aRate) {
                if($aRate["Type"] != "X") continue;
                if($aRate["Date"] != $dDate) $bPull = true;
                $aReturn["Data"][$sISO] = $aRate;
                $sPull .= $sISO.",";
            }

            if($bPull) {
                $sAPI   = "https://min-api.cryptocompare.com/data/pricehistorical?";
                $sAPI  .= "fsym=EUR&tsyms=".rtrim($sPull,",")."&ts=".$dDate;
                $aJson  = $this->getJsonData($sAPI);
                $SQL    = "";
                foreach($aRates as $sISO=>$aRate) {
                    if($aRate["Type"] != "X") continue;
                    if(array_key_exists($sISO, $aJson["EUR"])) {
                        $SQL .= "INSERT INTO euro_exchange (";
                        $SQL .= "Date, CurrencyID, Rate, RateDate, Acquired";
                        $SQL .= ") VALUES (";
                        $SQL .= $this->dbWrap($dDate,"date").", ";
                        $SQL .= $this->dbWrap($aRate["ID"],"int").", ";
                        $SQL .= $this->dbWrap($aJson["EUR"][$sISO],"float").", ";
                        $SQL .= $this->dbWrap($dDate,"date").", ";
                        $SQL .= $this->dbWrap(time(),"datetime").") ";
                        $SQL .= "ON DUPLICATE KEY UPDATE ";
                        $SQL .= "Date = VALUES(Date), ";
                        $SQL .= "CurrencyID = VALUES(CurrencyID), ";
                        $SQL .= "Rate = VALUES(Rate), ";
                        $SQL .= "RateDate = VALUES(RateDate), ";
                        $SQL .= "Acquired = VALUES(Acquired);\n";
                    }
                    $aReturn["Data"][$sISO]["Date"]     = $dDate;
                    $aReturn["Data"][$sISO]["Rate"]     = $aJson["EUR"][$sISO];
                    $aReturn["Data"][$sISO]["RateDate"] = $dDate;
                }
                if($SQL != "") {
                    $oRes = $this->db->multi_query($SQL);
                    while($this->db->more_results()) $this->db->next_result();

                    if(!$oRes) {
                        echo "MySQL Query Failed ...<br />";
                        echo "Error: ".$this->db->error."<br />";
                        echo "The Query was:<br />";
                        echo str_replace("\n","<br />",$SQL);
                    }
                }
            }
            $aReturn["Meta"]["Pull"] = $bPull;

            return $aReturn;
        }

        // Pull and parse JSON data from API
        private function getJsonData($sAPI) {

            global $cUserAgent;

            $webOpts    = array("http" => array("header" => $cUserAgent));
            $webContext = stream_context_create($webOpts);
            $jsonData   = @file_get_contents($sAPI,false,$webContext);

            if($jsonData === false) {
                return false;
            }

            if(in_array("Content-Encoding: deflate",$http_response_header)) {
                $jsonData = gzinflate($jsonData);
            }

            return json_decode($jsonData,true);
        }
    }
?>
