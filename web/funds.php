<?php
   /**
    *  Where's My Money? – Funds File
    * ================================
    *  Created 2017-05-31
    */

    $bMain = true;
    require_once("includes/init.php");

    $sView = htmGet("Part",1,false,"Summary");
    $aParts = array(
        "Summary" => array("Title"=>"Funds Summary","Menu"=>"Summary","URL"=>"funds.php?Part=Summary"),
        "Banks"   => array("Title"=>"Manage Banks", "Menu"=>"Banks",  "URL"=>"funds.php?Part=Banks"),
        "Trans"   => array("Title"=>"Transactions", "Menu"=>"",       "URL"=>""),
    );

   /**
    *  Page Content
    */

    // Header
    require_once("includes/header.php");
    makePageHeader($aParts,$sView);

    switch($sView) {
        case "Summary":
            include_once("parts/funds_summary.php");
            break;
        case "Banks":
            include_once("parts/funds_bank.php");
            break;
        case "Trans":
            include_once("parts/funds_transactions.php");
            break;
    }

    require_once("includes/footer.php");
?>
