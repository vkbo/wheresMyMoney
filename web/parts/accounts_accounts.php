<?php
   /**
    *  Where's My Money? – Accounts/Accounts Parts File
    * ==================================================
    *  Created 2017-06-08
    */

    $thisPage  = "accounts.php?Part=Accounts";
    $theAccs   = new Accounts($oDB);
    $aAccounts = $theAccs->getData();

    echo "<a href='".$thisPage."&Action=New'>Add Account</a>";

    $prevTitle = "";
    $oddEven   = 0;
    echo "<table class='list-table'>\n";
    foreach($aAccounts["Data"] as $aRow) {
        $currTitle = $aRow["Type"];
        if($currTitle != $prevTitle) {
            echo "<tr class='list-section'>";
                echo "<td colspan=5>".$cTypes["AccTypes"][$aRow["Type"]]."</td>";
            echo "</tr>\n";
            echo "<tr class='list-head'>";
                echo "<td colspan=2>Account</td>";
                echo "<td>Valid From</td>";
                echo "<td>Valid To</td>";
            echo "</tr>\n";
            $prevTitle = $currTitle;
            $oddEven   = 0;
        }
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            echo "<td class='mono'>".$aRow["Code"]."</td>";
            echo "<td><a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."'>".$aRow["AccountName"]."</a></td>";
            echo "<td>".($aRow["ValidFrom"] == 0 ? "&nbsp;" : date($cDateS,$aRow["ValidFrom"]))."</td>";
            echo "<td>".($aRow["ValidTo"] == 0 ? "&nbsp;" : date($cDateS,$aRow["ValidTo"]))."</td>";
        echo "</tr>\n";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=8>";
            echoTiming($aAccounts["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
?>
