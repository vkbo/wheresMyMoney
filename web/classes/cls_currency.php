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

        private function getJsonData($sAPI) {

            $webOpts    = array("http" => array("header" => "User-Agent: Mozilla/5.0"));
            $webContext = stream_context_create($webOpts);
            $jsonData   = @file_get_contents($sAPI,false,$webContext);

            if($jsonData === false) {
                return false;
            }

            if(in_array("Content-Encoding: deflate",$http_response_header)) {
                $jsonData = gzinflate($jsonData);
                echo getTimeStamp()." Unpacking data\n";
            }

            return json_decode($jsonData,true);
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
            $SQL .= "ID, Country, Name, ISO, Symbol, Type, Factor, RefCurrency ";
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

        public function getXRates($dDate,$xBase) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content"  => "XRates",
                    "Count"    => 0,
                    "Base"     => $xBase,
                    "BaseDate" => 0,
                ),
                "Data" => array(),
            );

            $aFiat = $this->getFiat($dDate);
            if($xBase == "EUR") {
                $baseRate = 1.0;
                $baseDate = strtotime(date("Y-m-d",time()));
            } elseif(array_key_exists($xBase, $aFiat)) {
                $baseRate = $aFiat[$xBase]["Rate"];
                $baseDate = $aFiat[$xBase]["RateDate"];
            } else {
                $baseRate = 0.0;
                $baseDate = 0;
            }
            foreach($aFiat as $sISO=>$aRate) {
                $aReturn["Data"][$sISO]["Rate"]     = $baseRate == 0 ? 0 : $aRate["Rate"]/$baseRate;
                $aReturn["Data"][$sISO]["RateDate"] = $aRate["RateDate"];
            }
            $aReturn["Meta"]["Count"]    = count($aReturn["Data"]);
            $aReturn["Meta"]["BaseDate"] = $baseDate;

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

            return $aReturn;
        }

        private function requestXRates($dDate) {

            $SQL  = "SELECT ";
            $SQL .= "c.ID AS ID, ";
            $SQL .= "c.ISO AS ISO, ";
            $SQL .= "c.Type AS Type, ";
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
            $SQL .= "LEFT JOIN euro_exchange AS ee ON ee.Date = tmp.Latest AND ee.CurrencyID = c.ID";
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
                    "Date"     => strtotime($aRow["Date"]),
                    "Rate"     => $aRow["Rate"],
                    "RateDate" => strtotime($aRow["RateDate"]),
                );
            }

            return $aReturn;
        }

        private function getFiat($dDate) {

            $iDelay  = 15*3600+750; // API is updated at 15:00. Setting delay to 15:15.
            $dDate   = time()-$dDate < $iDelay ? $dDate-$iDelay : $dDate;
            $dDate   = strtotime(date("Y-m-d",$dDate));
            $sAPI    = "http://api.fixer.io/latest?base=EUR&date=".date("Y-m-d",$dDate);

            $bPull   = false;
            $aReturn = array();
            $aRates  = $this->requestXRates($dDate);
            foreach($aRates as $sISO=>$aRate) {
                if($aRate["Type"] != "F") continue;
                $aReturn[$sISO] = $aRate;
                if($sISO == "EUR") {
                    $aReturn[$sISO]["Date"]     = $dDate;
                    $aReturn[$sISO]["Rate"]     = 1.0;
                    $aReturn[$sISO]["RateDate"] = $dDate;
                    continue;
                }
                if($aRate["Date"] != $dDate) $bPull = true;
            }

            if($bPull) {
                $aFiat = $this->getJsonData($sAPI);
                $SQL   = "";
                foreach($aRates as $sISO=>$aRate) {
                    if($aRate["Type"] != "F") continue;
                    if($sISO == "EUR") continue;
                    if(array_key_exists($sISO, $aFiat["rates"])) {
                        $SQL .= "INSERT INTO euro_exchange (";
                        $SQL .= "Date, CurrencyID, Rate, RateDate, Acquired";
                        $SQL .= ") VALUES (";
                        $SQL .= $this->dbWrap($dDate,"date").", ";
                        $SQL .= $this->dbWrap($aRate["ID"],"int").", ";
                        $SQL .= $this->dbWrap($aFiat["rates"][$sISO],"float").", ";
                        $SQL .= $this->dbWrap($aFiat["date"],"text").", ";
                        $SQL .= $this->dbWrap(time(),"datetime").") ";
                        $SQL .= "ON DUPLICATE KEY UPDATE ";
                        $SQL .= "Date = VALUES(Date), ";
                        $SQL .= "CurrencyID = VALUES(CurrencyID), ";
                        $SQL .= "Rate = VALUES(Rate), ";
                        $SQL .= "RateDate = VALUES(RateDate), ";
                        $SQL .= "Acquired = VALUES(Acquired);\n";
                    }
                    echo "Ping!<br />";
                    $aReturn[$sISO]["Date"]     = $dDate;
                    $aReturn[$sISO]["Rate"]     = $aFiat["rates"][$sISO];
                    $aReturn[$sISO]["RateDate"] = strtotime($aFiat["date"]);
                }
                if($SQL == "") return true;

                $oRes = $this->db->multi_query($SQL);
                while($this->db->more_results()) $this->db->next_result();

                if(!$oRes) {
                    echo "MySQL Query Failed ...<br />";
                    echo "Error: ".$this->db->error."<br />";
                    echo "The Query was:<br />";
                    echo str_replace("\n","<br />",$SQL);
                }
            }

            return $aReturn;
        }
    }
?>
