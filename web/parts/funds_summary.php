<?php
   /**
    *  Where's My Money? – Funds/Summary Parts File
    * ===============================================
    *  Created 2017-05-31
    */

    $convertTo = $theOpt->getValue("BaseCurrency");
    $convertTo = htmGet("ConvertTo",1,false,$convertTo);
    $thisPage  = "funds.php?Part=Summary";

    $theFunds  = new Funds($oDB);
    $aFunds    = $theFunds->getData();

    $theCurrs  = new Currency($oDB);
    $aCurrs    = $theCurrs->getData();

    if($convertTo != "") {
        $theOpt->setValue("BaseCurrency",$convertTo);
        $aRates = $theCurrs->getXRates(time(),$convertTo);
    }

    // Start Two Column Content
    echo "<div class='content-wrapper'>\n";
    echo "<div class='content-main'>\n";
    // ========================

    $prevTitle = "";
    $oddEven   = 0;
    echo "<table class='list-table'>\n";
    foreach($aFunds["Data"] as $aRow) {
        $currTitle = $aRow["Type"].$aRow["BankName"];
        if($currTitle != $prevTitle) {
            echo "<tr class='list-section'>";
                if($aRow["Type"] == "B") {
                    echo "<td colspan=5>".$aRow["BankName"]."</td>";
                } else {
                    echo "<td colspan=5>".$cTypes["Funds"][$aRow["Type"]]."</td>";
                }
            echo "</tr>\n";
            echo "<tr class='list-head'>";
                echo "<td>Name</td>";
                echo "<td>Account Number</td>";
                echo "<td>Category</td>";
                echo "<td colspan=2 class='right'>Balance</td>";
                echo "<td class='tbl-clear'>&nbsp;&nbsp;</td>";
                echo "<td colspan=2 class='right'>Converted</td>";
            echo "</tr>\n";
            $prevTitle = $currTitle;
            $oddEven   = 0;
        }
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            echo "<td><a href='funds.php?Part=Trans&FundsID=".$aRow["ID"]."'>".$aRow["FundsName"]."</a></td>";
            echo "<td class='mono'>".rdblLongStr($aRow["AccountNumber"],18,"N/A")."</td>";
            echo "<td>".$cTypes["FundsCat"][$aRow["Category"]]."</td>";
            echo "<td class='mono'>".$aRow["CurrencyISO"]."</td>";
            echo "<td class='mono right'>".rdblAmount($aRow["Balance"],$aRow["Factor"],0,4)."</td>";
            echo "<td class='tbl-clear'>&nbsp;&nbsp;</td>";
            echo "<td class='mono'>".$convertTo."</td>";
            if(array_key_exists($aRow["CurrencyISO"], $aRates["Data"])) {
                $xRate   = $aRates["Data"][$aRow["CurrencyISO"]]["Rate"];
                $xFactor = $aRates["Data"][$aRow["CurrencyISO"]]["Factor"];
                $xAmount = $aRow["Balance"]*$xFactor/$xRate;
            } else {
                $xAmount = 0.0;
            }
            echo "<td class='mono right'>".rdblAmount($xAmount,$aRates["Base"]["Factor"],0,4)."</td>";
        echo "</tr>\n";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=8>";
            echoTiming($aRates["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";

    // Separate Two Column Content
    echo "</div>\n";
    echo "<div class='content-aside'>\n";
    // ===========================

    echo "<div class='toolbox'>\n";
    echo "<h3>Exchange</h3>\n";
    echo "<h5>Convert to</h5>\n";
    foreach($aCurrs["Data"] as $aCurr) {
        if(!$aCurr["RefCurrency"]) continue;
        echo "<div><a href='".$thisPage."&ConvertTo=".$aCurr["ISO"]."'>".$aCurr["ISO"]." - ".$aCurr["Name"]."</a></div>";
    }
    echo "<br />\n";
    echo "<h5>Rates</h5>\n";
    echo "<table class='list-table'>\n";
    echo "<tr class='list-head'>";
        echo "<td>ISO</td>";
        echo "<td>Rate</td>";
        echo "<td>Date</td>";
    echo "</tr>\n";
    foreach($aRates["Data"] as $sISO=>$aRate) {
        echo "<tr>";
            if($aRate["Rate"] < 0.01) {
                echo "<td class='mono right'>m".$sISO."</td>";
                echo "<td class='mono right'>".rdblNum($aRate["Rate"]*1000,4)."</td>";
            } elseif($aRate["Rate"] > 1000) {
                echo "<td class='mono right'>k".$sISO."</td>";
                echo "<td class='mono right'>".rdblNum($aRate["Rate"]/1000,4)."</td>";
            } else {
                echo "<td class='mono right'>".$sISO."</td>";
                echo "<td class='mono right'>".rdblNum($aRate["Rate"],4)."</td>";
            }
            echo "<td>".date($cDateS,$aRate["RateDate"])."</td>";
        echo "</tr>\n";
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=3>";
            if($aRates["Meta"]["Pull"]) {
                echo "<span class='green'>New rates pulled</span><br />";
            }
            echoTiming($aRates["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";

    // End Two Column Content
    echo "</div>\n";
    echo "</div>\n";
    // ======================

?>
