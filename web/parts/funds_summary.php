<?php
   /**
    *  Where's My Money? – Funds/Summary Parts File
    * ===============================================
    *  Created 2017-05-31
    */

    $convertTo = $theOpt->getValue("BaseCurrency","EUR");
    $convertTo = htmGet("ConvertTo",1,false,$convertTo);
    $theOpt->setValue("BaseCurrency",$convertTo);

    $showYear  = $theOpt->getValue("ShowYear",date("Y",time()));
    $showYear  = htmGet("Year",1,false,$showYear);
    $ratesDate = $showYear == date("Y",time()) ? time() : strtotime($showYear."-01-01");
    $theOpt->setValue("ShowYear",$showYear);

    $thisFile  = "funds.php";
    $thisPage  = "funds.php?Part=Summary";

    $theFunds  = new Funds($oDB);
    $theFunds->setFilter("Year",$showYear);
    $aFunds    = $theFunds->getData();
    $aYears    = $theFunds->getYears();
    $aToDo     = $theFunds->toAccount();

    $theCurrs  = new Currency($oDB);
    $aCurrs    = $theCurrs->getData();
    $aRates    = $theCurrs->getXRates($ratesDate,$convertTo);

    // Start Two Column Content
    echo "<div class='content-wrapper'>\n";
    echo "<div class='content-main'>\n";
    // ========================

    $nToDo = $aToDo["Data"]["ToDo"];
    if($nToDo > 0) {
        $sCol = $nToDo < 50 ? "msg-warn" : "msg-err";
        echo "<div class='".$sCol."'>";
            echo $nToDo." transactions need accounting.";
        echo "</div>";
        echo "<div class='msg-time'>";
            echoTiming($aToDo["Meta"]["Time"]);
        echo "</div><br />\n";
    }

    echo "<div>";
        echo "<b>Year:</b>&nbsp;";
        foreach($aYears as $selYear) {
            echo "<a href='".$thisPage."&Year=".$selYear."'>".$selYear."</a>";
            if($selYear !== end($aYears)) echo "&nbsp;|&nbsp;";
        }
        echo "<div class='floatr'>";
            echo "[&nbsp;";
                echo "<a href='funds.php?Part=Funds&Action=New'>Add Funds</a>";
            echo "&nbsp;]";
        echo "</div>";
    echo "</div><br />\n";

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
                $xAmount = $aRow["Balance"]*($xRate == 0 ? 0 : $xFactor/$xRate);
            } else {
                $xAmount = 0.0;
            }
            echo "<td class='mono right'>".rdblAmount($xAmount,$aRates["Base"]["Factor"],0,4)."</td>";
        echo "</tr>\n";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=8>";
            echoTiming($aFunds["Meta"]["Time"]);
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
            echoTiming($aRates["Meta"]["Time"]);
            if($aRates["Meta"]["Pull"]) {
                echo "<span class='green'>&nbsp;[Pulled New]</span><br />";
            }
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";

    // End Two Column Content
    echo "</div>\n";
    echo "</div>\n";
    // ======================

?>
