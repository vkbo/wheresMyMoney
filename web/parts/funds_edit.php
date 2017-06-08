<?php
   /**
    *  Where's My Money? – Funds/Edit Parts File
    * ===========================================
    *  Created 2017-06-07
    */

    $updateID  = htmGet("ID",0,false,"");
    $thisPage  = "funds.php?Part=Funds";

    $showYear  = $theOpt->getValue("ShowYear");

    $theFunds  = new Funds($oDB);
    $theFunds->setFilter("Year",$showYear);
    $theCurrs  = new Currency($oDB);
    $aCurrs    = $theCurrs->getData();
    $theBanks  = new Bank($oDB);
    $aBanks    = $theBanks->getData();


    if($doAction == "New") {
        echo "<h2>Funds and Bank Accounts</h2>\n";
        echo "<h3>Adding Funds</h3>\n";
        $frmName          = "";
        $frmAccountNumber = "";
        $frmSwiftIBAN     = "";
        $frmType          = "";
        $frmCategory      = "";
        $frmBankID        = "";
        $frmCurrencyID    = "";
        $frmOpened        = date($cDateS,time());
        $frmClosed        = "";
    } else {
        $aFunds = $theFunds->getData($updateID);
        echo "<h2>".$aFunds["Data"][0]["FundsName"]."</h2>\n";
        echo "<h3>Editing Entry</h3>\n";

        $frmUpdateID      = $aFunds["Data"][0]["ID"];
        $frmName          = $aFunds["Data"][0]["FundsName"];
        $frmAccountNumber = $aFunds["Data"][0]["AccountNumber"];
        $frmSwiftIBAN     = $aFunds["Data"][0]["SwiftIBAN"];
        $frmType          = $aFunds["Data"][0]["Type"];
        $frmCategory      = $aFunds["Data"][0]["Category"];
        $frmBankID        = $aFunds["Data"][0]["BankID"];
        $frmCurrencyID    = $aFunds["Data"][0]["CurrencyID"];
        $frmOpened        = $aFunds["Data"][0]["Opened"];
        $frmClosed        = $aFunds["Data"][0]["Closed"];
        $frmOpened        = $frmOpened == "" ? "" : date($cDateS,$frmOpened);
        $frmClosed        = $frmClosed == "" ? "" : date($cDateS,$frmClosed);
    }

    echo "<form method='post' action='".$thisPage."&Action=Save'>\n";
    echo "<table class='form-table'>\n";
    echo "<tr>";
        echo "<th>Name</th>";
        echo "<td><input type='text' name='Name' class='input-w' value='".$frmName."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Account Number</th>";
        echo "<td><input type='text' name='AccountNumber' class='input-w' value='".$frmAccountNumber."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Swift/IBAN</th>";
        echo "<td><input type='text' name='SwiftIBAN' class='input-w' value='".$frmSwiftIBAN."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Funds Type</th>";
        echo "<td>";
            echo "<select name='Type'>";
                foreach($cTypes["Funds"] as $sCode=>$sValue) {
                    echo "<option value='".$sCode."'".($frmType==$sCode?" selected":"").">".$sValue."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Funds Category</th>";
        echo "<td>";
            echo "<select name='Category'>";
                foreach($cTypes["FundsCat"] as $sCode=>$sValue) {
                    echo "<option value='".$sCode."'".($frmCategory==$sCode?" selected":"").">".$sValue."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Bank</th>";
        echo "<td>";
            echo "<select name='BankID'>";
                echo "<option value=''>&nbsp;</option>";
                foreach($aBanks["Data"] as $aRow) {
                    echo "<option value='".$aRow["ID"]."'".($frmBankID==$aRow["ID"]?" selected":"").">".$aRow["Name"]."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Currency</th>";
        echo "<td>";
            echo "<select name='CurrencyID'>";
                foreach($aCurrs["Data"] as $aRow) {
                    echo "<option value='".$aRow["ID"]."'".($frmCurrencyID==$aRow["ID"]?" selected":"").">".$aRow["ISO"]."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Opened Date</th>";
        echo "<td><input type='text' name='Opened' class='input-s center' value='".$frmOpened."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Closed Date</th>";
        echo "<td><input type='text' name='Closed' class='input-s center' value='".$frmClosed."' /></td>";
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
