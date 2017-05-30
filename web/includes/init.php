<?php
   /**
    *  Where's My Money? – Init File
    * ===============================
    *  Created 2017-05-30
    */

    if(!isset($bMain)) exit();

    require_once("config.php");
    require_once("includes/functions.php");

    $oDB = new mysqli($cDBHost,$cDBUser,$cDBPass,$cDBMain);
?>
