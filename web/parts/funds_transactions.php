<?php
   /**
    *  Where's My Money? – Funds/Transactions Parts File
    * ===================================================
    *  Created 2017-06-01
    */

    $fundsID = htmGet("FundsID",0,false,0);
    $thisPage = "funds.php?Part=Trans&FundsID=".$fundsID;

    $theFunds = new Funds($oDB);
    $aFunds   = $theFunds->getData($fundsID);
    $fundsFac = $aFunds["Data"][0]["Factor"];

    $theTrans = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);
    $aTrans   = $theTrans->getData();


    $oddEven  = 0;

    echo "<table class='list-table'>\n";
    echo "<tr class='list-head'>";
        echo "<td>Date</td>";
        echo "<td>Details</td>";
        echo "<td>Tr. Date</td>";
        echo "<td colspan=2 class='right'>Currency</td>";
        echo "<td class='right'>Amount</td>";
        echo "<td colspan=2>&nbsp;</td>";
    echo "</tr>";
    foreach($aTrans["Data"] as $iKey=>$aRow) {
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            echo "<td>".rdblDate($aRow["RecordDate"],$cDateS)."</td>";
            echo "<td>".$aRow["Details"]."</td>";
            echo "<td>".rdblDate($aRow["TransactionDate"],$cDateS)."</td>";
            echo "<td class='mono'>".$aRow["Currency"]."</td>";
            echo "<td class='mono right'>".rdblAmount($aRow["Original"],$aRow["CurrencyFac"])."</td>";
            echo "<td class='mono right'>".rdblAmount($aRow["Amount"],$fundsFac)."</td>";
            echo "<td><a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."' title='Edit'>";
                echo "<img src='images/icon_gtk_edit_col.png' alt='Edit' /></a></td>";
            echo "<td><a href='".$thisPage."&Action=Delete&ID=".$aRow["ID"]."' title='Delete'>";
                echo "<img src='images/icon_gtk_delete_col.png' alt='Delete' /></a></td>";
        echo "</tr>";
        $oddEven++;
    }
    echo "<tr class='list-stats'><td colspan=8>Query: ".number_format($aTrans["Meta"]["Time"],2)." ms</td></tr>";
    echo "</table>\n";
    echo "<a href='import.php?Type=Trans&ID=".$fundsID."'>Import Transactions</a><br />\n";
?>
