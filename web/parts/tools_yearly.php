<?php
   /**
    *  Where's My Money? – Tools/Yearly Parts File
    * =============================================
    *  Created 2017-06-08
    */

    $doYear   = htmGet("Year",1,false,"");
    $fundsID  = htmGet("ID",0,false,0);
    $doAction = htmGet("Action",1,false,"");
    $thisPage = "tools.php?Part=Yearly";
    $theTrans = new Transact($oDB);

    echo "<h2>Accounting Years:</h2>\n";

    if($doYear == "") {

        $aYearly = $theTrans->getYearlyStatus();

        $oddEven = 0;
        echo "<table class='list-table'>\n";
        echo "<tr class='list-head'>";
            echo "<td>Year</td>";
            echo "<td colspan=2>Funds</td>";
            echo "<td colspan=4>Status</td>";
            echo "<td>Actions</td>";
        echo "</tr>";
        foreach($aYearly["Data"] as $iKey=>$aRow) {
            if($aRow["Year"] == date("Y",time())) {
                echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
                    echo "<td>".$aRow["Year"]."</td>";
                    echo "<td class='right'>".$aRow["Funds"]."</td>";
                    echo "<td>accounts</td>";
                    echo "<td colspan=5>Current year</td>";
                echo "</tr>";
            } else {
                $bDone = $aRow["Funds"] == $aRow["Locked"];
                echo "<tr class='list-row ".($bDone?"g-":"r-").($oddEven%2==0?"even":"odd")."'>";
                    echo "<td>".$aRow["Year"]."</td>";
                    echo "<td class='right'>".$aRow["Funds"]."</td>";
                    echo "<td>accounts</td>";
                    echo "<td class='right'>".$aRow["Count"]."</td>";
                    echo "<td>summed,</td>";
                    echo "<td class='right'>".$aRow["Locked"]."</td>";
                    echo "<td>locked</td>";
                    if($bDone) {
                        echo "<td><b>Finalised</b></td>";
                    } else {
                        echo "<td><a href='".$thisPage."&Year=".$aRow["Year"]."'>Manage</a></td>";
                    }
                echo "</tr>";
            }
            $oddEven++;
        }
        echo "<tr class='list-stats'>";
            echo "<td colspan=4>";
                echoTiming($aYearly["Meta"]["Time"]);
            echo "</td>";
        echo "</tr>\n";
        echo "</table>\n";

    } else {

        if($doAction == "Recalc") {
            $theTrans->calcYear($doYear,$fundsID);
        }
        if($doAction == "Lock") {
            $theTrans->lockYear($doYear,$fundsID);
        }

        $aYearly = $theTrans->getYearlyStatus($doYear);

        $oddEven = 0;
        echo "<table class='list-table'>\n";
        echo "<tr class='list-head'>";
            echo "<td>Funds</td>";
            echo "<td colspan=2>Count</td>";
            echo "<td colspan=2 class='right'>Balance</td>";
            echo "<td class='right'>Summed</td>";
            echo "<td>Action</td>";
        echo "</tr>\n";
        foreach($aYearly["Data"] as $iKey=>$aRow) {
            if($aRow["Locked"] == 0) {
                if($aRow["Balance"] == $aRow["Summed"]) {
                    $bLock = true;
                    $bCalc = false;
                    $sCol  = "o-";
                } else {
                    $bLock = false;
                    $bCalc = true;
                    $sCol  = "r-";
                }
            } else {
                $bLock = false;
                $bCalc = true;
                $sCol  = "g-";
            }
            echo "<tr class='list-row ".$sCol.($oddEven%2==0?"even":"odd")."'>";
                echo "<td>".$aRow["FundsName"]."</td>";
                echo "<td class='mono right'>".$aRow["Count"]."</td>";
                echo "<td>entries</td>";
                echo "<td class='mono'>".$aRow["Currency"]."</td>";
                echo "<td class='mono right'>".rdblAmount($aRow["Balance"],$aRow["Factor"],0,4)."</td>";
                echo "<td class='mono right'>".rdblAmount($aRow["Summed"],$aRow["Factor"],0,4)."</td>";
                echo "<td>";
                    if($bCalc) echo "<a href='".$thisPage."&Year=".$doYear."&ID=".$aRow["ID"]."&Action=Recalc'>Recalculate</a>";
                    if($bLock) echo "<a href='".$thisPage."&Year=".$doYear."&ID=".$aRow["ID"]."&Action=Lock'>Lock</a>";
                echo "&nbsp;</td>";
            echo "</tr>\n";
            $oddEven++;
        }
        echo "<tr class='list-stats'>";
            echo "<td colspan=4>";
                echoTiming($aYearly["Meta"]["Time"]);
            echo "</td>";
        echo "</tr>\n";
        echo "</table>\n";

    }
?>
