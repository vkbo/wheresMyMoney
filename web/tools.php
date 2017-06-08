<?php
   /**
    *  Where's My Money? – Tools File
    * ================================
    *  Created 2017-06-08
    */

    $bMain  = true;
    require_once("includes/init.php");
    $theOpt = new Settings($oDB);

    $sView    = htmGet("Part",1,false,"Tools");
    $doAction = htmGet("Action",1,false,"List");
    $aParts   = array(
        "Tools"  => array("Title"=>"Accounting Tools",            "Menu"=>"All Tools","URL"=>"tools.php?Part=Tools"),
        "Yearly" => array("Title"=>"Recalculate or Finalise Year","Menu"=>"",         "URL"=>""),
    );

   /**
    *  Page Content
    */

    // Header
    require_once("includes/header.php");
    makePageHeader($aParts,$sView);

    switch($sView) {
        case "Tools":  include_once("parts/tools_tools.php"); break;
        case "Yearly": include_once("parts/tools_yearly.php"); break;
        default: echo "<p>Nothing to display.</p>"; break;
    }

    require_once("includes/footer.php");
?>
