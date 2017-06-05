<?php
   /**
    *  Where's My Money? – Init File
    * ===============================
    *  Created 2017-05-30
    */

    if(!isset($bMain)) exit();

    require_once("config.php");
    $oDB = new mysqli($cDBHost,$cDBUser,$cDBPass,$cDBMain);
    $oDB->set_charset("utf8");

    // Set data arrays
    $cTypes = array(
        "Currency" => array(
            "F" => "Fiat Currency",
            "X" => "Crypto Currency",
        ),
        "Funds" => array(
            "B" => "Bank Account",
            "C" => "Cash",
            "X" => "Crypto Currency",
        ),
        "FundsCat" => array(
            "P" => "Spending",
            "S" => "Savings",
            "C" => "Credit",
        ),
    );

    // Include Classes
    require_once("classes/cls_bank.php");
    require_once("classes/cls_currency.php");
    require_once("classes/cls_funds.php");
    require_once("classes/cls_transactions.php");

    // Include functions
    require_once("includes/functions.php");
    require_once("includes/layout.php");

?>
