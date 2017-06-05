<?php
   /**
    *  Where's My Money? – Transaction/Save Parts File
    * =================================================
    *  Created 2017-06-05
    */

    $fundsID   = htmGet("FundsID",0,false,0);
    $pageNum   = htmGet("Page",0,false,1);
    $fromDate  = htmGet("FromDate",1,false,"");
    $thisPage  = "funds.php?Part=Trans&FundsID=".$fundsID;

    $theFunds  = new Funds($oDB);
    $aFunds    = $theFunds->getData($fundsID);
    $aDetails  = $aFunds["Data"][0];
    $fundsFac  = $aDetails["Factor"];
    $isoCurr   = $aDetails["CurrencyISO"];
    $fundsType = $aDetails["Type"];

    $theCurrs  = new Currency($oDB);
    $theTrans  = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);

    $frmID          = trim(htmPost("ID",""));
    $frmRecordDate  = trim(htmPost("RecordDate",""));
    $frmTransDate   = trim(htmPost("TransactionDate",""));
    $frmBlockHeight = trim(htmPost("BlockHeight",""));
    $frmTransHash   = trim(htmPost("TransactionHash",""));
    $frmDetails     = trim(htmPost("Details",""));
    $frmOriginal    = trim(htmPost("Original",""));
    $frmCurrencyID  = trim(htmPost("CurrencyID",""));
    $frmAmount      = trim(htmPost("Amount",""));

    $aData["ID"]              = $frmID == "" ? 0 : intval($frmID);
    $aData["RecordDate"]      = $frmRecordDate == "" ? time() : strtotime($frmRecordDate);
    $aData["TransactionDate"] = $frmTransDate == "" ? $aData["RecordDate"] : strtotime($frmTransDate);
    $aData["BlockHeight"]     = $frmBlockHeight == "" ? null : intval($frmBlockHeight);
    $aData["TransactionHash"] = $frmTransHash == "" ? null : trim($frmTransHash);
    $aData["Details"]         = $frmDetails == "" ? null : cleanMultLineString($frmDetails);
    $aData["Amount"]          = $frmAmount == "" ? null : cleanAmountString($frmAmount,$fundsFac);
    $aData["CurrencyID"]      = $frmCurrencyID == "" ? null : intval($frmCurrencyID);
    if(!is_null($aData["CurrencyID"]) && $frmOriginal != "") {
        $aCurrs  = $theCurrs->getData($aData["CurrencyID"]);
        $iFactor = $aCurrs["Data"][0]["Factor"];
        $aData["Original"] = cleanAmountString($frmOriginal,$iFactor);
    } else {
        $aData["Original"] = null;
    }

    $theTrans->saveData(array(0=>$aData));

    header("Location: ".$thisPage);
?>
