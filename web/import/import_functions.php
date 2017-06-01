<?php
   /**
    *  Where's My Money? – Common Import Functions
    * =============================================
    *  Created 2017-06-01
    */

    function robustValueParser($rawValue, $iFactor=100) {

        $rawValue = trim($rawValue);
        $rawValue = str_replace(" ","",$rawValue);

        $iDelim = strrpos($rawValue,",")-strlen($rawValue);
        if($iDelim == -2 || $iDelim == -3) {
            $rawValue = substr_replace($rawValue,".",$iDelim,1);
        }
        $rawValue = str_replace(",","",$rawValue);

        return round(floatval($rawValue)*$iFactor);
    }
?>
