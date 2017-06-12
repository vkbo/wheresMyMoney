<?php
   /**
    *  Where's My Money? – Accounts/Summary Parts File
    * =================================================
    *  Created 2017-06-08
    */

    $thisPage  = "accounts.php?Part=Accounts";
    $theAccLn  = new Accounting($oDB);
    $aAccounts = $theAccLn->getAccounts();

    $prevTitle = "";
    $aBalance  = array();
    $aTypeSum  = array();
    $nCols     = 0;
    $oddEven   = 0;
    // print_r($aAccounts);
    echo "<table class='list-table'>\n";
    echo "<colgroup>";
        echo "<col width='0%' />"; $nCols++;
        echo "<col width='0%' />"; $nCols++;
        foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
            echo "<col width='0%' />"; $nCols++;
            $aBalance[$sISO] = 0;
            $aTypeSum[$sISO] = 0;
        }
        echo "<col width='100%' />"; $nCols++;
    echo "</colgroup>\n";
    foreach($aAccounts["Data"] as $aRow) {
        $currTitle = $aRow["Type"];
        if($currTitle != $prevTitle) {
            if($prevTitle != "") {
                echo "<tr class='list-foot even'>";
                    echo "<td colspan=2>Total</td>";
                    foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
                        echo "<td class='mono right'>".rdblAmount($aTypeSum[$sISO],$iFactor,0,4)."</td>";
                    }
                    echo "<td class='tbl-clear'>&nbsp</td>";
                echo "</tr>\n";
            }
            echo "<tr class='list-section'>";
                echo "<td colspan=".$nCols.">".$cTypes["AccTypes"][$aRow["Type"]]."</td>";
            echo "</tr>\n";
            echo "<tr class='list-head'>";
                echo "<td colspan=2>Account</td>";
                foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
                    echo "<td class='right'>".$sISO."</td>";
                    $aTypeSum[$sISO] = 0;
                }
                echo "<td class='tbl-clear'>&nbsp</td>";
            echo "</tr>\n";
            $prevTitle = $currTitle;
            $oddEven   = 0;
        }
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            echo "<td class='mono'>".$aRow["Code"]."</td>";
            echo "<td><a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."'>".$aRow["Name"]."</a></td>";
            foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
                $iDebit  = 0;
                $iCredit = 0;
                if(array_key_exists("Balance", $aRow)) {
                    if(array_key_exists($sISO, $aRow["Balance"])) {
                        $iDebit  = $aRow["Balance"][$sISO]["SumDebit"];
                        $iCredit = $aRow["Balance"][$sISO]["SumCredit"];
                    }
                }
                echo "<td class='mono right'>".rdblAmount($iDebit-$iCredit,$iFactor,0,4)."</td>";
                $aBalance[$sISO] += $iDebit - $iCredit;
                $aTypeSum[$sISO] += $iDebit - $iCredit;
            }
            echo "<td class='tbl-clear'>&nbsp</td>";
        echo "</tr>\n";
        $oddEven++;
    }
    echo "<tr class='list-foot even'>";
        echo "<td colspan=2>Total</td>";
        foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
            echo "<td class='mono right'>".rdblAmount($aTypeSum[$sISO],$iFactor,0,4)."</td>";
        }
        echo "<td class='tbl-clear'>&nbsp</td>";
    echo "</tr>\n";
    echo "<tr class='list-section'>";
        echo "<td colspan=".$nCols.">Totals</td>";
    echo "</tr>\n";
    echo "<tr class='list-head'>";
        echo "<td colspan=2>Account</td>";
        foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
            echo "<td class='right'>".$sISO."</td>";
        }
        echo "<td class='tbl-clear'>&nbsp</td>";
    echo "</tr>\n";
    echo "<tr class='list-row even'>";
        echo "<td colspan=2>Balance</td>";
        foreach($aAccounts["Columns"]["Balance"] as $sISO=>$iFactor) {
            echo "<td class='mono right'>".rdblAmount($aBalance[$sISO],$iFactor,0,4)."</td>";
        }
        echo "<td class='tbl-clear'>&nbsp</td>";
    echo "</tr>\n";
    echo "<tr class='list-stats'>";
        echo "<td colspan=8>";
            echoTiming($aAccounts["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
?>
