<?php
   /**
    *  Where's My Money? – Funds/Bank Parts File
    * ===========================================
    *  Created 2017-05-31
    */

    $theBanks = new Bank($oDB);

    $aBanks = $theBanks->getEntry();
    print_r($aBanks);
?>
