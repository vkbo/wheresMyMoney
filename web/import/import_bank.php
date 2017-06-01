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

        $theTrans = new Transact($oDB);

        $aImport = call_user_func("importParseBank_".$scriptType, $rawData);

        // print_r($aImport);

        $iCount  = $aImport["Meta"]["Count"];
        $dateMin = $aImport["Meta"]["DateMin"];
        $dateMax = $aImport["Meta"]["DateMax"];

        $theTrans->setFilter("FundsID",$fundsID);
        $theTrans->setFilter("FromDate",$dateMin-7*86400);

        //$theTrans->saveTemp($aImport["Data"],true);

        $aExists = $theTrans->getData();

        $toc = microtime(true);
        $aReturn["Data"]         = $aImport["Data"];
        $aReturn["Exists"]       = $aExists["Data"];
        $aReturn["Meta"]["Time"] = ($toc-$tic)*1000;

        return $aReturn;
    }
?>
