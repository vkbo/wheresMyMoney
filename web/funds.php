<?php
   /**
    *  Where's My Money? – Funds File
    * ================================
    *  Created 2017-05-31
    */

    $bMain  = true;
    require_once("includes/init.php");
    $theOpt = new Settings($oDB);

    $sView    = htmGet("Part",1,false,"Summary");
    $doAction = htmGet("Action",1,false,"List");
    $aParts   = array(
        "Summary" => array("Title"=>"Funds Summary","Menu"=>"Summary",  "URL"=>"funds.php?Part=Summary"),
        "Banks"   => array("Title"=>"Manage Banks", "Menu"=>"Banks",    "URL"=>"funds.php?Part=Banks"),
        "Trans"   => array("Title"=>"Transactions", "Menu"=>"",         "URL"=>""),
        "Funds"   => array("Title"=>"Add Funds",    "Menu"=>"",         "URL"=>""),
        "Account" => array("Title"=>"Accounting",   "Menu"=>"",         "URL"=>""),
    );

   /**
    *  Page Content
    */

    // Header
    require_once("includes/header.php");
    makePageHeader($aParts,$sView);

    switch($sView) {
        case "Summary": include_once("parts/funds_summary.php"); break;
        case "Banks":   include_once("parts/funds_bank.php");    break;
        case "Trans":
            switch($doAction) {
                case "List": include_once("parts/funds_transactions.php"); break;
                case "Edit": include_once("parts/transaction_edit.php");   break;
                case "New":  include_once("parts/transaction_edit.php");   break;
                case "Save": include_once("parts/transaction_save.php");   break;
                default: echo "<p>Nothing to display.</p>"; break;
            }; break;
        case "Funds":
            switch($doAction) {
                case "Edit": include_once("parts/funds_edit.php"); break;
                case "New":  include_once("parts/funds_edit.php"); break;
                case "Save": include_once("parts/funds_save.php"); break;
                default: echo "<p>Nothing to display.</p>"; break;
            }; break;
        default: echo "<p>Nothing to display.</p>"; break;
    }

    require_once("includes/footer.php");
?>
