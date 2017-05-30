<?php
   /**
    *  Where's My Money? – Main Functions
    * ====================================
    *  Created 2017-05-30
    */

    function rdblNum($dValue, $iDecimals=2, $sUnit="") {
        $dValue = floatval($dValue);
        if(is_infinite($dValue)) return "&infin; ".$sUnit;
        return trim(number_format($dValue,$iDecimals,"."," ")." ".$sUnit);
    }
?>
