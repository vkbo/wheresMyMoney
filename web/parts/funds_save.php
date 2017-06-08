<?php
   /**
    *  Where's My Money? – Funds/Save Parts File
    * ===========================================
    *  Created 2017-06-07
    */

    $theFunds = new Funds($oDB);

    $frmID            = trim(htmPost("ID",""));
    $frmName          = trim(htmPost("Name",""));
    $frmAccountNumber = trim(htmPost("AccountNumber",""));
    $frmSwiftIBAN     = trim(htmPost("SwiftIBAN",""));
    $frmType          = trim(htmPost("Type",""));
    $frmCategory      = trim(htmPost("Category",""));
    $frmBankID        = trim(htmPost("BankID",""));
    $frmCurrencyID    = trim(htmPost("CurrencyID",""));
    $frmOpened        = trim(htmPost("Opened",""));
    $frmClosed        = trim(htmPost("Closed",""));

    $aData["ID"]            = $frmID == "" ? 0 : intval($frmID);
    $aData["Name"]          = $frmName == "" ? null : cleanMultLineString($frmName);
    $aData["AccountNumber"] = $frmAccountNumber == "" ? null : cleanMultLineString($frmAccountNumber);
    $aData["SwiftIBAN"]     = $frmSwiftIBAN == "" ? null : cleanMultLineString($frmSwiftIBAN);
    $aData["Type"]          = $frmType == "" ? null : $frmType;
    $aData["Category"]      = $frmCategory == "" ? null : $frmCategory;
    $aData["BankID"]        = $frmBankID == "" ? null : intval($frmBankID);
    $aData["CurrencyID"]    = $frmCurrencyID == "" ? null : intval($frmCurrencyID);
    $aData["Opened"]        = $frmOpened == "" ? null : strtotime($frmOpened);
    $aData["Closed"]        = $frmClosed == "" ? null : strtotime($frmClosed);

    print_r($aData);

    $bOK = $theFunds->saveData(array(0=>$aData));

    if($bOK) header("Location: funds.php?Part=Summary");
?>
