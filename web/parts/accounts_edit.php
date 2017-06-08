<?php
   /**
    *  Where's My Money? – Funds/Edit Parts File
    * ===========================================
    *  Created 2017-06-07
    */

    $updateID  = htmGet("ID",0,false,"");
    $thisPage  = "accounts.php?Part=Accounts";

    $theAccs   = new Accounts($oDB);

    if($doAction == "New") {
        echo "<h2>Accounts</h2>\n";
        echo "<h3>Adding Account</h3>\n";
        $frmName        = "";
        $frmType        = "";
        $frmCode        = "";
        $frmDescription = "";
        $frmValidFrom   = date($cDateS,time());
        $frmValidTo     = "";
    } else {
        $aAccounts = $theAccs->getData($updateID);
        echo "<h2>Accounts</h2>\n";
        echo "<h3>Editing Account</h3>\n";

        $frmUpdateID    = $aAccounts["Data"][0]["ID"];
        $frmName        = $aAccounts["Data"][0]["AccountName"];
        $frmType        = $aAccounts["Data"][0]["Type"];
        $frmCode        = $aAccounts["Data"][0]["Code"];
        $frmDescription = $aAccounts["Data"][0]["Description"];
        $frmValidFrom   = $aAccounts["Data"][0]["ValidFrom"];
        $frmValidTo     = $aAccounts["Data"][0]["ValidTo"];
        $frmValidFrom   = $frmValidFrom == "" ? "" : date($cDateS,$frmValidFrom);
        $frmValidTo     = $frmValidTo == "" ? "" : date($cDateS,$frmValidTo);
    }

    echo "<form method='post' action='".$thisPage."&Action=Save'>\n";
    echo "<table class='form-table'>\n";
    echo "<tr>";
        echo "<th>Account Name</th>";
        echo "<td><input type='text' name='Name' class='input-w' value='".$frmName."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Account Type</th>";
        echo "<td>";
            echo "<select name='Type'>";
                foreach($cTypes["AccTypes"] as $sCode=>$sValue) {
                    echo "<option value='".$sCode."'".($frmType==$sCode?" selected":"").">".$sValue."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Account Code</th>";
        echo "<td><input type='text' name='Code' class='input-s' value='".$frmCode."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Description</th>";
        echo "<td>";
            echo "<textarea name='Description' class='text-w'>".$frmDescription."</textarea>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Valid From</th>";
        echo "<td><input type='text' name='ValidFrom' class='input-s center' value='".$frmValidFrom."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Valid To</th>";
        echo "<td><input type='text' name='ValidTo' class='input-s center' value='".$frmValidTo."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<td colspan=2 class='input-button'>";
            echo "<input type='hidden' name='ID' value='".$frmUpdateID."' />";
            echo "<input type='submit' />";
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>\n";
?>
