<?php
   /**
    *  Where's My Money? – Main Functions
    * ====================================
    *  Created 2017-05-30
    */

   /**
    *  Data Functions
    */

    // Parse $_POST data
    function htmPost($sVar, $vDefault="") {
        if(array_key_exists($sVar, $_POST)) {
            return $_POST[$sVar];
        } else {
            return $vDefault;
        }
    }

    // Parse $_GET data
    function htmGet($sVar, $iType, $bMeta, $vDefault="") {
        if(array_key_exists($sVar, $_GET)) {
            $vData = $_GET[$sVar];
            if($iType == 0) { // Number
                if(is_numeric($vData)) {
                    return $vData;
                } else {
                    return $vDefault;
                }
            } elseif($iType == 1) { // String
                if(is_string($vData)) {
                    $vMeta = htmlentities($vData, ENT_QUOTES);
                    if($bMeta == true) {
                        return $vMeta;
                    } else {
                        if(strlen($vMeta) > strlen($vData)) {
                            return $vDefault;
                        } else {
                            return $vData;
                        }
                    }
                } else {
                    return $vDefault;
                }
            }
        } else {
            return $vDefault;
        }
    }

   /**
    *  Formatting Functions
    */

    function rdblNum($dValue, $iDecimals=2, $sUnit="") {
        $dValue = floatval($dValue);
        if(is_infinite($dValue)) return "&infin; ".$sUnit;
        return trim(number_format($dValue,$iDecimals,"."," ")." ".$sUnit);
    }

    function rdblBigNum($dValue, $iDecimals=2, $sUnit="", $maxIt=6) {
        $dValue = floatval($dValue);
        if(is_infinite($dValue)) return "&infin; ".$sUnit;
        if(is_null($dValue))     return "";
        $aScale = array("","k","M","G","T","P","E");
        $iScale = 0;
        while($dValue >= 1000.0 && $iScale < $maxIt) {
            $dValue /= 1000.0;
            $iScale++;
        }
        return trim(number_format($dValue,$iDecimals,"."," ")." ".$aScale[$iScale].$sUnit);
    }

    function rdblSmallNum($dValue, $iDecimals=2, $sUnit="", $maxIt=6) {
        $dValue = floatval($dValue);
        if(is_infinite($dValue)) return "&infin; ".$sUnit;
        if(is_null($dValue))     return "";
        $aScale = array("","m","µ","n","p","f","a");
        $iScale = 0;
        while($dValue < 1.0 && $iScale < $maxIt) {
            $dValue *= 1000.0;
            $iScale++;
        }
        return trim(number_format($dValue,$iDecimals,"."," ")." ".$aScale[$iScale].$sUnit);
    }

    function rdblAmount($iVal, $iFac=100, $iDec=0, $iMax=12) {

        if(is_null($iVal)) return "";
        if($iDec == 0)     $iDec = log10($iFac);
        if($iDec > $iMax)  $iDec = $iMax;

        $sReturn  = "<span class='amount ";
        $sReturn .= $iVal < 0 ? "red" : "black";
        $sReturn .= "'>";
        $sReturn .= number_format($iVal/$iFac,$iDec,"."," ");
        $sReturn .= "</span>";

        return $sReturn;
    }

    function rdblDate($iVal, $sFormat) {
        return $iVal === null ? "&nbsp;" : date($sFormat,intval($iVal));
    }

    function rdblLongStr($sValue, $nMax, $nullString="") {
        if(is_null($sValue)) return $nullString;
        if(strlen($sValue) > $nMax) {
            return substr($sValue,0,$nMax-3)."...";
        } else {
            return $sValue;
        }
    }

    function cleanMultLineString($sValue) {
        $sValue = str_replace("\r", "", $sValue);
        $sValue = str_replace("\n", " ", $sValue);
        $sValue = str_replace("\t", " ", $sValue);
        $sValue = str_replace("  ", " ", $sValue);
        $sValue = str_replace("  ", " ", $sValue);
        return trim($sValue);
    }

    function cleanAmountString($sValue, $iFactor) {
        $iComma = strrpos($sValue, ",");
        if($iComma !== false) {
            $sValue = substr_replace($sValue, ".", $iComma, 1);
        }
        $sValue = str_replace(" ", "", $sValue);
        $sValue = str_replace(",", "", $sValue);
        $sValue = str_replace("'", "", $sValue);
        return round(floatval($sValue)*$iFactor);
    }

   /**
    *  Math Functions
    */

    function getSign($dValue) {
        return round(abs($dValue)/$dValue);
    }

?>
