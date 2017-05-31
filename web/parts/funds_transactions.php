<?php
   /**
    *  Where's My Money? – Funds/Transactions Parts File
    * ===================================================
    *  Created 2017-06-01
    */

    $fundsID = htmGet("FundsID",0,false,0);

    $theFunds = new Funds($oDB);
    $aFunds   = $theFunds->getEntry($fundsID);

    $theTrans = new Transact($oDB);
    $theTrans->setFiler("FundsID",$fundsID);
    $aTrans   = $theTrans->getEntry();

    print_r($aTrans);
?>
