<?php
   /**
    *  Where's My Money? – Transaction/Edit Parts File
    * =================================================
    *  Created 2017-06-05
    */

    $fundsID   = htmGet("FundsID",0,false,0);
    $updateID  = htmGet("ID",0,false,"");
    $thisPage  = "funds.php?Part=Trans&FundsID=".$fundsID;

    $theFunds  = new Funds($oDB);
    $aFunds    = $theFunds->getData($fundsID);
    $aDetails  = $aFunds["Data"][0];
    $fundsFac  = $aDetails["Factor"];
    $isoCurr   = $aDetails["CurrencyISO"];
    $fundsType = $aDetails["Type"];

    $theTrans  = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);
    $lstDetail = $theTrans->getLastDetails();

    $theCurrs  = new Currency($oDB);
    $aCurrs    = $theCurrs->getData();

    echo "<h2>".$aDetails["FundsName"]."</h2>\n";

    if($doAction == "New") {
        echo "<h3>Adding Entry</h3>\n";
        $frmRecordDate  = date("d.m.Y",time());
        $frmTransDate   = "";
        $frmBlockHeight = "";
        $frmTransHash   = "";
        $frmDetails     = "";
        $frmOriginal    = "";
        $frmCurrencyID  = "";
        $frmAmount      = "";
        $frmUpdateID    = "";
    } else {
        echo "<h3>Editing Entry</h3>\n";
        $aTrans         = $theTrans->getData($updateID);
        $frmRecordDate  = date($cDateS,$aTrans["Data"][0]["RecordDate"]);
        $frmTransDate   = date($cDateS,$aTrans["Data"][0]["TransactionDate"]);
        $frmBlockHeight = $aTrans["Data"][0]["BlockHeight"];
        $frmTransHash   = $aTrans["Data"][0]["TransactionHash"];
        $frmDetails     = $aTrans["Data"][0]["Details"];
        $frmOriginal    = $aTrans["Data"][0]["Original"];
        $frmCurrencyID  = $aTrans["Data"][0]["CurrencyID"];
        $frmAmount      = $aTrans["Data"][0]["Amount"]/$fundsFac;
        $frmUpdateID    = $aTrans["Data"][0]["ID"];
    }

    echo "<form method='post' action='".$thisPage."&Action=Save'>\n";
    echo "<table class='form-table'>\n";
    echo "<tr>";
        echo "<th>Record Date</th>";
        echo "<td><input type='text' name='RecordDate' class='input-s center' value='".$frmRecordDate."' /></td>";
    echo "</tr>\n";
    switch($fundsType) {
    case "B";
        echo "<tr>";
            echo "<th>Transaction Date</th>";
            echo "<td><input type='text' name='TransactionDate' class='input-s center' value='".$frmTransDate."' /></td>";
        echo "</tr>\n";
        break;
    case "X";
        echo "<tr>";
            echo "<th>Block Height</th>";
            echo "<td><input type='text' name='BlockHeight' class='input-s center' value='".$frmBlockHeight."' /></td>";
        echo "</tr>\n";
        echo "<tr>";
            echo "<th>Transaction Hash</th>";
            echo "<td><input type='text' name='TransactionHash' class='input-w' value='".$frmTransHash."' /></td>";
        echo "</tr>\n";
        break;
    }
    echo "<tr>";
        echo "<th>Details</th>";
        echo "<td>";
            echo "<select class='input-w margin-b' onChange='setDetails(this)'>";
                echo "<option value='0'>&nbsp;</option>";
                $cleanArray = "'',";
                $idx = 0;
                foreach($lstDetail["Data"] as $aRow) {
                    $cleanString  = str_replace("'", "\'", $aRow["Details"]);
                    $cleanArray  .= "'".$cleanString."',";
                    $idx++;
                    echo "<option value='".$idx."'>".$cleanString."</option>";
                }
            echo "</select><br />";
            echo "<script>\n";
            echo "function setDetails(ctx) {\n";
            echo "    aOpt = [".$cleanArray."];\n";
            echo "    document.getElementById('Details').innerHTML = aOpt[ctx.value];\n";
            echo "}\n";
            echo "</script>\n";
            echo "<textarea name='Details' id='Details' class='mono text-w'>".$frmDetails."</textarea>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Original Amount</th>";
        echo "<td>";
            echo "<input type='text' name='Original' class='input-s center' value='".$frmOriginal."' />&nbsp;";
            echo "<select name='CurrencyID'>";
                echo "<option value=''>&nbsp;</option>";
                foreach($aCurrs["Data"] as $aRow) {
                    echo "<option value='".$aRow["ID"]."'".($frmCurrencyID==$aRow["ID"]?" selected":"").">".$aRow["ISO"]."</option>";
                }
            echo "</select>";
        echo "</td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<th>Amount</th>";
        echo "<td><input type='text' name='Amount' class='input-s center' value='".$frmAmount."' /></td>";
    echo "</tr>\n";
    echo "<tr>";
        echo "<td colspan=2 class='input-button'>";
            echo "<input type='hidden' name='ID' value='".$frmUpdateID."' />";
            echo "<input type='submit' />";
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</form>\n";
?>
