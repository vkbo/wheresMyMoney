<?php
   /**
    *  Where's My Money? – Funds/Transactions Parts File
    * ===================================================
    *  Created 2017-06-01
    */

    $fundsID   = htmGet("FundsID",0,false,0);
    $pageNum   = htmGet("Page",0,false,1);
    $fromDate  = htmGet("FromDate",1,false,"");

    $thisPage  = "funds.php?Part=Trans&FundsID=".$fundsID;

    $showYear  = $theOpt->getValue("ShowYear");
    $doPages   = $fromDate == "";
    $pageSize  = 50;

    $theFunds  = new Funds($oDB);
    $theFunds->setFilter("Year",$showYear);
    $aFunds    = $theFunds->getData($fundsID);
    $aDetails  = $aFunds["Data"][0];
    $fundsFac  = $aDetails["Factor"];
    $isoCurr   = $aDetails["CurrencyISO"];
    $fundsType = $aDetails["Type"];

    $theTrans  = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);
    $theTrans->setFilter("FromDate",strtotime($showYear."-01-01"));
    $theTrans->setFilter("ToDate",strtotime($showYear."-12-31"));
    $theTrans->setFilter("PageSize",$pageSize);
    $theTrans->setFilter("PageNum",$pageNum);
    $aTrans    = $theTrans->getData(0,$doPages);
    $nTrans    = $theTrans->getCount();

    $nPages    = ceil($nTrans/$pageSize);

    echo "<h2>".$aDetails["FundsName"]." for ".$showYear."</h2>\n";
    echo "<table class='display-table'>\n";
    if(!is_null($aDetails["BankName"])) {
        echo "<tr>";
            echo "<th>Bank</th>";
            echo "<td>".$aDetails["BankName"]."</td>";
        echo "</tr>\n";
    }
    echo "<tr>";
        echo "<th>Account Type</th>";
        echo "<td>".$cTypes["Funds"][$aDetails["Type"]];
        echo ", ".$cTypes["FundsCat"][$aDetails["Category"]]."</td>";
    echo "</tr>\n";
    if(!is_null($aDetails["AccountNumber"])) {
        echo "<tr>";
            echo "<th>Account Number</th>";
            echo "<td>".rdblLongStr($aDetails["AccountNumber"],32)."</td>";
        echo "</tr>\n";
    }
    echo "<tr>";
        echo "<th>Balance</th>";
        echo "<td>".rdblAmount($aDetails["Balance"],$fundsFac)." ".$isoCurr."</td>";
    echo "</tr>\n";
    echo "</table>\n";

    echo "<div>";
        echo "<b>Actions:</b>&nbsp;";
        echo "<a href='funds.php?Part=Funds&Action=Edit&ID=".$fundsID."'>Edit Funds</a>";
        echo "&nbsp;|&nbsp;";
        echo "<a href='import.php?Type=Trans&ID=".$fundsID."'>Import Entries</a>";
        echo "&nbsp;|&nbsp;";
        echo "<a href='".$thisPage."&Action=New'>Add Entry</a>";
        echo "&nbsp;|&nbsp;";
        if($doPages && $nTrans > 0) {
            echo "<a href='".$thisPage."&FromDate=".date("Y-01-01",time())."'>Show Full Year</a>";
            echo "<div class='floatr'>";
                echoPagination($thisPage,$pageNum,$nPages);
            echo "</div><br />\n";
        } else {
            echo "<a href='".$thisPage."&Page=1'>Show Pages</a>";
        }
    echo "</div><br />\n";

    $oddEven = 0;
    $nCols   = 6;
    echo "<table class='list-table'>\n";
    echo "<tr class='list-head'>";
        echo "<td><img src='images/icon_accounts.png' /></td>";
        echo "<td>Date</td>";
        echo "<td>Details</td>";
        if($fundsType == "X") {
            $nCols = 7;
            echo "<td>Tr. Hash</td>";
            echo "<td>Height</td>";
        } else {
            echo "<td>Tr. Date</td>";
        }
        echo "<td colspan=2 class='right'>Currency</td>";
        echo "<td class='right'>Amount</td>";
        // echo "<td colspan=2>&nbsp;</td>";
    echo "</tr>";
    foreach($aTrans["Data"] as $iKey=>$aRow) {
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            if($aRow["Amount"] == $aRow["AccTotal"]) {
                echo "<td class='center green'>&#10004;</td>";
            } else {
                echo "<td class='center red'>&#10006;</td>";
            }
            echo "<td>".rdblDate($aRow["RecordDate"],$cDateS)."</td>";
            echo "<td class='expand'><a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."' title='Edit'>";
                echo $aRow["Details"]."</a></td>";
            if($fundsType == "X") {
                echo "<td title='".$aRow["TransactionHash"]."'>".rdblLongStr($aRow["TransactionHash"],15)."</td>";
                echo "<td>".$aRow["BlockHeight"]."</td>";
            } else {
                echo "<td>".rdblDate($aRow["TransactionDate"],$cDateS)."</td>";
            }
            echo "<td class='mono'>".$aRow["Currency"]."</td>";
            echo "<td class='mono right'>".rdblAmount($aRow["Original"],$aRow["CurrencyFac"])."</td>";
            echo "<td class='mono right'>".rdblAmount($aRow["Amount"],$fundsFac)."</td>";
            // echo "<td><a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."' title='Edit'>";
            //     echo "<img src='images/icon_gtk_edit_col.png' alt='Edit' /></a></td>";
            // echo "<td><a href='".$thisPage."&Action=Delete&ID=".$aRow["ID"]."' title='Delete'>";
            //     echo "<img src='images/icon_gtk_delete_col.png' alt='Delete' /></a></td>";
        echo "</tr>";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=".$nCols.">";
            echoTiming($aTrans["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    echo "</table>\n";

    if($doPages && $nTrans > 0) {
        echo "<div class='right'>";
            echoPagination($thisPage,$pageNum,$nPages);
        echo "</div><br />\n";
    }
?>
