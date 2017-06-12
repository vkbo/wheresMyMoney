<?php
   /**
    *  Where's My Money? – Accounts File
    * ===================================
    *  Created 2017-06-08
    */

    $bMain  = true;
    require_once("includes/init.php");
    $theOpt = new Settings($oDB);

    $sView    = htmGet("Part",1,false,"Summary");
    $doAction = htmGet("Action",1,false,"List");
    $aParts   = array(
        "Summary"  => array("Title"=>"Accounts Summary",       "Menu"=>"Summary",     "URL"=>"accounts.php?Part=Summary"),
        "Accounts" => array("Title"=>"Accounts",               "Menu"=>"Accounts",    "URL"=>"accounts.php?Part=Accounts"),
        "Trans"    => array("Title"=>"Accounting Transactions","Menu"=>"Transactions","URL"=>"accounts.php?Part=Trans"),
    );

   /**
    *  Page Content
    */

    // Header
    require_once("includes/header.php");
    makePageHeader($aParts,$sView);

    switch($sView) {
        case "Summary":  include_once("parts/accounts_summary.php");  break;
        case "Accounts":
            switch($doAction) {
                case "List": include_once("parts/accounts_accounts.php"); break;
                case "Edit": include_once("parts/accounts_edit.php");     break;
                case "New":  include_once("parts/accounts_edit.php");     break;
                case "Save": include_once("parts/accounts_save.php");     break;
                default: echo "<p>Nothing to display.</p>"; break;
            }; break;
        case "Trans":
            switch($doAction) {
                case "List": include_once("parts/accounting_transactions.php"); break;
                default: echo "<p>Nothing to display.</p>"; break;
            }; break;
        default: echo "<p>Nothing to display.</p>"; break;
    }

    require_once("includes/footer.php");
?>
