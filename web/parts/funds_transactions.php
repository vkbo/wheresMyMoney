<?php
   /**
    *  Where's My Money? – Funds/Transactions Parts File
    * ===================================================
    *  Created 2017-06-01
    */

    $fundsID   = htmGet("FundsID",0,false,0);
    $pageNum   = htmGet("Page",0,false,1);
    $fromDate  = htmGet("FromDate",1,false,"");
    $nAccount  = htmGet("Account",0,false,0);

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
    $fundsAcc  = $aDetails["AccountID"];

    $theTrans  = new Transact($oDB);
    $theTrans->setFilter("FundsID",$fundsID);
    $theTrans->setFilter("FromDate",strtotime($showYear."-01-01"));
    $theTrans->setFilter("ToDate",strtotime($showYear."-12-31"));
    $theTrans->setFilter("PageSize",$pageSize);
    $theTrans->setFilter("PageNum",$pageNum);
    $theTrans->setFilter("AccountingDone",$nAccount == 0 ? null : false);
    $theTrans->setOrder($nAccount == 0 ? "DESC" : "ASC");
    $aTrans    = $theTrans->getData(0,$doPages);
    $nTrans    = $theTrans->getCount();
    $nPages    = ceil($nTrans/$pageSize);

    $theAccs   = new Accounts($oDB);
    $accSelect = $theAccs->generateSelections($showYear);

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
    if(!is_null($aDetails["AccountID"])) {
        echo "<tr>";
            echo "<th>Default Account</th>";
            echo "<td>".$aDetails["AccountCode"]." - ".$aDetails["AccountName"]."</td>";
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
        echo "<a href='".$thisPage."&Account=".$pageSize."'>Accounting</a>";
        echo "&nbsp;|&nbsp;";
        if($doPages && $nTrans > 0 && $nAccount == 0) {
            echo "<a href='".$thisPage."&FromDate=".date("Y-01-01",time())."'>Show Full Year</a>";
            echo "<div class='floatr'>";
                echoPagination($thisPage,$pageNum,$nPages);
            echo "</div><br />\n";
        } else {
            echo "<a href='".$thisPage."&Page=1'>Show Pages</a>";
        }
    echo "</div><br />\n";

    $oddEven = 0;
    $nCols   = 0;
    if($nAccount > 0) {
        echo "<form method='post' action='".$thisPage."&Action=Acc'>\n";
    }
    echo "<table class='list-table'>\n";
    echo "<colgroup>";
        echo "<col width='0%' />";
        echo "<col width='0%' />";
        echo "<col width='100%' />";
        if($fundsType == "X") {
            echo "<col width='0%' />";
        }
        if($nAccount == 0) {
            echo "<col width='0%' />";
            echo "<col width='0%' />";
        }
        echo "<col width='0%' />";
        echo "<col width='0%' />";
        echo "<col width='0%' />";
    echo "</colgroup>";
    echo "<tr class='list-head'>";
        echo "<td><img src='images/icon_accounts.png' /></td>"; $nCols++;
        echo "<td>Date</td>"; $nCols++;
        echo "<td>Details</td>"; $nCols++;
        if($fundsType == "X") {
            echo "<td>Tr. Hash</td>"; $nCols++;
            echo "<td>Height</td>"; $nCols++;
        } else {
            echo "<td>Tr. Date</td>"; $nCols++;
        }
        if($nAccount == 0) {
            echo "<td colspan=2 class='right'>Currency</td>"; $nCols++; $nCols++;
            echo "<td class='right'>Rate</td>"; $nCols++;
        }
        echo "<td class='right'>Amount</td>"; $nCols++;
        if($nAccount > 0) {
            echo "<td>Against Account</td>"; $nCols++;
        }
    echo "</tr>\n";
    foreach($aTrans["Data"] as $iKey=>$aRow) {
        echo "<tr class='list-row ".($oddEven%2==0?"even":"odd")."'>";
            // if(is_null($aRow["AccountingID"])) {
            //     echo "<td class='center red'>&#10006;</td>";
            // } else {
            //     echo "<td class='center green'>&#10004;</td>";
            // }
            if(is_null($aRow["AccCount"])) {
                $sTitle = "No accounting.";
                echo "<td class='red' title='".$sTitle."'>N/A</td>";
            } else {
                if($aRow["AccCount"] == 1) {
                    $sTitle = $aRow["AccCode"]." - ".$aRow["AccName"];
                    echo "<td class='blue' title='".$sTitle."'>".$aRow["AccCode"]."</td>";
                } elseif($aRow["AccCount"] > 1) {
                    $sTitle = "Not implemented.";
                    echo "<td class='green' title='".$sTitle."'>Multi</td>";
                } else {
                    $sTitle = "Ambiguous result.";
                    echo "<td class='red' title='".$sTitle."'>N/A</td>";
                }
            }
            echo "<td>".rdblDate($aRow["RecordDate"],$cDateS)."</td>";
            echo "<td class='expand'>";
                echo "<a href='".$thisPage."&Action=Edit&ID=".$aRow["ID"]."' title='Edit'>".$aRow["Details"]."</a>";
            echo "</td>";
            if($fundsType == "X") {
                echo "<td title='".$aRow["TransactionHash"]."'>".rdblLongStr($aRow["TransactionHash"],15)."</td>";
                echo "<td>".$aRow["BlockHeight"]."</td>";
            } else {
                echo "<td>".rdblDate($aRow["TransactionDate"],$cDateS)."</td>";
            }
            if($nAccount == 0) {
                echo "<td class='mono'>".$aRow["Currency"]."</td>";
                $xRate = $aRow["Original"] === null ? "&nbsp;" : rdblNum($aRow["Amount"]/$aRow["Original"],3);
                echo "<td class='mono right'>".rdblAmount($aRow["Original"],$aRow["CurrencyFac"])."</td>";
                echo "<td class='mono right blue'>".$xRate."</td>";
            }
            echo "<td class='mono right'>".rdblAmount($aRow["Amount"],$fundsFac)."</td>";
            if($nAccount > 0) {
                echo "<td>";
                    echo "<input type='hidden' name='TransID[]' value='".$aRow["ID"]."' />";
                    echo "<select name='AccountID[]'>";
                        echo "<option value=''>None</option>";
                        echo $accSelect;
                    echo "</select>";
                echo "</td>";
            }
        echo "</tr>\n";
        $oddEven++;
    }
    echo "<tr class='list-stats'>";
        echo "<td colspan=".$nCols.">";
            echoTiming($aTrans["Meta"]["Time"]);
        echo "</td>";
    echo "</tr>\n";
    if($nAccount > 0) {
        echo "<tr class='list-stats'>";
            echo "<td colspan=".$nCols." class='input-button'>";
                echo "<input type='submit' />";
            echo "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    if($nAccount > 0) {
        echo "</form>\n";
    }

    if($doPages && $nTrans > 0 && $nAccount == 0) {
        echo "<div class='right'>";
            echoPagination($thisPage,$pageNum,$nPages);
        echo "</div><br />\n";
    }
?>
