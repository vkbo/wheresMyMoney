<?php
   /**
    *  Where's My Money? – Funds/Funds Parts File
    * ============================================
    *  Created 2017-05-31
    */

    $theFunds = new Funds($oDB);

    $aFunds = $theFunds->getEntry();

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
            echo "</tr>";
            echo "<tr class='list-head'>";
                echo "<td>Name</td>";
                echo "<td>Account Number</td>";
                echo "<td>Category</td>";
                echo "<td colspan=2 class='right'>Balance</td>";
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
        echo "</tr>";
        $oddEven++;
    }
    echo "<tr class='list-stats'><td colspan=5>Query: ".number_format($aFunds["Meta"]["Time"],2)." ms</td></tr>";
    echo "</table>\n";
?>
