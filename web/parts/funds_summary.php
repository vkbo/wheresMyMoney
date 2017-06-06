<?php
   /**
    *  Where's My Money? – Funds/Summary Parts File
    * ===============================================
    *  Created 2017-05-31
    */

    $convertTo = htmGet("ConvertTo",1,false,"");
    $thisPage  = "funds.php?Part=Summary";

    $theFunds  = new Funds($oDB);
    $aFunds    = $theFunds->getData();

    $theCurrs  = new Currency($oDB);
    $aCurrs    = $theCurrs->getData();

    if($convertTo != "") {
        $aRates = $theCurrs->getXRates(time(),$convertTo);
    }

    $prevTitle = "";
    $oddEven   = 0;
    echo "<table class='list-table' style='display: inline-block;'>\n";
    foreach($aFunds["Data"] as $aRow) {
        $currTitle = $aRow["Type"].$aRow["BankName"];
        if($currTitle != $prevTitle) {
            echo "<tr class='list-section'>";
                if($aRow["Type"] == "B") {
                    echo "<td colspan=5>".$aRow["BankName"]."</td>";
                } else {
                    echo "<td colspan=5>".$cTypes["Funds"][$aRow["Type"]]."</td>";
                }
            echo "</tr>";
            echo "<tr class='list-head'>";
                echo "<td>Name</td>";
                echo "<td>Account Number</td>";
                echo "<td>Category</td>";
                echo "<td colspan=2 class='right'>Balance</td>";
                echo "<td class='tbl-clear'>&nbsp;&nbsp;</td>";
                echo "<td colspan=2 class='right'>Converted</td>";
            echo "</tr>";
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
        echo "</tr>";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=8>Query: ".number_format($aFunds["Meta"]["Time"],2)." ms</td>";
    echo "</tr>";
    echo "</table>\n";

    echo "<div class='toolbox'>";
    echo "<h3>Exchange</h3>";
    echo "<h5>Convert to</h5>";
    foreach($aCurrs["Data"] as $aCurr) {
        if(!$aCurr["RefCurrency"]) continue;
        echo "<div><a href='".$thisPage."&ConvertTo=".$aCurr["ISO"]."'>".$aCurr["ISO"]." - ".$aCurr["Name"]."</a></div>";
    }
    echo "<br />";
    echo "<h5>Rates</h5>";
    echo "<table class='list-table'>";
    echo "<tr class='list-head'>";
        echo "<td>ISO</td>";
        echo "<td>Rate</td>";
        echo "<td>Date</td>";
    echo "</tr>";
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
        echo "</tr>";
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=3>";
            if($aRates["Meta"]["Pull"]) {
                echo "<span class='green'>New rates pulled</span><br />";
            }
            echo "Query: ".number_format($aRates["Meta"]["Time"],2)." ms";
        echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";

?>
