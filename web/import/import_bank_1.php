<?php
   /**
    *  Where's My Money? – Import Function No 1
    * ==========================================
    *  Created 2017-06-01
    */

    function importParseBank_NO_Sparebank1_csv($rawData) {

        $aReturn = array(
            "Meta" => array(
                "Content" => "RawImport",
                "Count"   => 0,
                "DateMin" => null,
                "DateMax" => null,
            ),
            "Data" => array(),
        );

        $dateMin = null;
        $dateMax = null;

        // Replace all semi colons with tabs so it works for both formats
        $rawData = str_replace("\r","",$rawData);
        $rawData = str_replace(";","\t",$rawData);

        $aLines = explode("\n",$rawData);
        foreach($aLines as $sLine) {

            $aBits = explode("\t",$sLine);
            if(count($aBits) < 4) continue;

            $rawRDate   = trim($aBits[0]) == "" ? null : strtotime(trim($aBits[0]));
            $rawText    = trim($aBits[1]) == "" ? null : trim($aBits[1]);
            $rawTDate   = trim($aBits[2]) == "" ? null : strtotime(trim($aBits[2]));
            $rawAmount  = trim($aBits[3]) == "" ? null : trim($aBits[3]);

            $rawCurr    = null;
            $rawOrig    = null;

            // Reject line if there's no date
            if(!is_numeric($rawRDate)) continue;

            // Parse Norwegian number format
            $rawAmount = str_replace(" ","", $rawAmount);
            $rawAmount = str_replace(",",".",$rawAmount);
            $rawAmount = round(floatval($rawAmount)*100);

            // If text starts with a * it's a VISA transaction
            // Trying to extract currency
            $aElems = explode(" ",$rawText);
            if(substr($rawText,0,1) == "*") {
                if(count($aElems) > 3) {
                    $rawCurr = $aElems[2];
                    $rawOrig = round(floatval($aElems[3])*100);
                    $rawOrig = getSign($rawAmount)*abs($rawOrig);
                }
            }

            $aReturn["Data"][] = array(
                "RecordDate"      => $rawRDate,
                "TransactionDate" => $rawTDate,
                "Details"         => $rawText,
                "Original"        => $rawOrig,
                "Currency"        => $rawCurr,
                "Amount"          => $rawAmount,
                "Hash"            => md5($rawRDate.":".$rawText.":".$rawAmount),
            );

            $dateMin = $dateMin === null ? $rawRDate : min($dateMin,$rawRDate);
            $dateMax = $dateMax === null ? $rawRDate : max($dateMax,$rawRDate);
        }

        $aReturn["Meta"]["Count"]   = count($aReturn["Data"]);
        $aReturn["Meta"]["DateMin"] = $dateMin;
        $aReturn["Meta"]["DateMax"] = $dateMax;
        $aReturn["Data"]            = array_reverse($aReturn["Data"]);

        return $aReturn;
    }
?>
