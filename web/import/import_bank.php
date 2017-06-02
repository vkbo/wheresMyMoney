<?php
   /**
    *  Where's My Money? – Import Wrapper Function
    * =============================================
    *  Created 2017-06-01
    */

    include_once("import/import_functions.php");
    include_once("import/import_bank_1.php");

    function importBank($scriptType, $fundsID, $rawData) {

        global $oDB;

        $tic = microtime(true);

        $aReturn = array(
            "Meta" => array(
                "Content" => "Transactions",
                "Count"   => 0,
                "Time"    => 0,
            ),
            "Data" => array(),
        );
        $aImport = call_user_func("importParseBank_".$scriptType, $rawData);

        $toc = microtime(true);
        $aReturn["Data"]         = $aImport["Data"];
        $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

        return $aReturn;
    }
?>
