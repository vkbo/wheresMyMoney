<?php
   /**
    *  Where's My Money? – Accounts/Save Parts File
    * ==============================================
    *  Created 2017-06-08
    */

    $theAccs = new Accounts($oDB);

    $frmID          = trim(htmPost("ID",""));
    $frmName        = trim(htmPost("Name",""));
    $frmType        = trim(htmPost("Type",""));
    $frmCode        = trim(htmPost("Code",""));
    $frmDescription = trim(htmPost("Description",""));
    $frmValidFrom   = trim(htmPost("ValidFrom",""));
    $frmValidTo     = trim(htmPost("ValidTo",""));

    $aData["ID"]          = $frmID == "" ? 0 : intval($frmID);
    $aData["Name"]        = $frmName == "" ? null : cleanMultLineString($frmName);
    $aData["Type"]        = $frmType == "" ? null : $frmType;
    $aData["Code"]        = $frmCode == "" ? null : cleanMultLineString($frmCode);
    $aData["Description"] = $frmDescription == "" ? null : cleanMultLineString($frmDescription);
    $aData["ValidFrom"]   = $frmValidFrom == "" ? null : strtotime($frmValidFrom);
    $aData["ValidTo"]     = $frmValidTo == "" ? null : strtotime($frmValidTo);

    $bOK = $theAccs->saveData(array(0=>$aData));

    if($bOK) header("Location: accounts.php?Part=Accounts");
?>
