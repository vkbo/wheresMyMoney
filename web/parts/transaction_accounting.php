<?php
   /**
    *  Where's My Money? – Transaction/Accounting Parts File
    * =======================================================
    *  Created 2017-06-11
    */

    $fundsID   = htmGet("FundsID",0,false,0);
    // $pageNum   = htmGet("Page",0,false,1);
    // $fromDate  = htmGet("FromDate",1,false,"");
    $thisPage  = "funds.php?Part=Trans&FundsID=".$fundsID;
    $showYear  = $theOpt->getValue("ShowYear");

    $theFunds  = new Funds($oDB);
    $theFunds->setFilter("Year",$showYear);
    $aFunds    = $theFunds->getData($fundsID);
    $fundsFac  = $aFunds["Data"][0]["Factor"];
    $fundsCurr = $aFunds["Data"][0]["CurrencyID"];
    $fundsAcc  = $aFunds["Data"][0]["AccountID"];
    // $fundsType = $aFunds["Data"][0]["Type"];

    // $theCurrs  = new Currency($oDB);
    // $theTrans  = new Transact($oDB);
    // $theTrans->setFilter("FundsID",$fundsID);

    $frmTransID   = htmPost("TransID",array());
    $frmAccountID = htmPost("AccountID",array());

    $theTrans = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);

    $theAccLn = new Accounting($oDB);

    $bOK = true;
    foreach($frmTransID as $iKey=>$transID) {
        $accountID = intval($frmAccountID[$iKey]);
        if($accountID == 0) continue;

        echo $accountID."<br />";

        $aTrans = $theTrans->getData($transID);
        if(count($aTrans["Data"]) == 0) continue;

        $recID      = $aTrans["Data"][0]["ID"];
        $recDate    = $aTrans["Data"][0]["RecordDate"];
        $recDetails = $aTrans["Data"][0]["Details"];
        $recAmount  = $aTrans["Data"][0]["Amount"];

        $debAmount  = $recAmount < 0 ? 0 : abs($recAmount);
        $creAmount  = $recAmount < 0 ? abs($recAmount) : 0;

        $aData[0] = array(
            "AccountID"  => $fundsAcc,
            "Details"    => $aTrans["Data"][0]["Details"],
            "CurrencyID" => $fundsCurr,
            "Debit"      => $debAmount,
            "Credit"     => $creAmount,
        );
        $aData[1] = array(
            "AccountID"  => $accountID,
            "Details"    => $aTrans["Data"][0]["Details"],
            "CurrencyID" => $fundsCurr,
            "Debit"      => $creAmount,
            "Credit"     => $debAmount,
        );

        $bOK = $bOK && $theAccLn->saveGroup(0, $recDate, $aData, 0, "transactions", $recID);
    }

    if($bOK) header("Location: ".$thisPage."&Account=50");
?>
