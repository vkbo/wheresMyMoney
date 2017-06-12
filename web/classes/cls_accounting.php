<?php
   /**
    *  Where's My Money? – Accounting Class
    * ======================================
    *  Created 2017-06-11
    */

    class Accounting extends DataBase
    {
        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
        }

        // Methods
        public function getAccounts($ID=0) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "AccountSummmary",
                    "Count"   => 0,
                ),
                "Data" => array(),
                "Columns" => array(
                    "Balance" => array(),
                ),
            );

            $SQL  = "SELECT ";
            $SQL .= "a.ID AS ID, ";
            $SQL .= "a.Type AS Type, ";
            $SQL .= "a.Name AS Name, ";
            $SQL .= "a.Code AS Code, ";
            $SQL .= "SUM(ac.Debit) AS SumDebit, ";
            $SQL .= "SUM(ac.Credit) AS SumCredit, ";
            $SQL .= "c.ISO AS Currency, ";
            $SQL .= "c.Factor AS Factor ";
            $SQL .= "FROM accounts AS a ";
            $SQL .= "LEFT JOIN accounting AS ac ON ac.AccountID = a.ID ";
            $SQL .= "LEFT JOIN currency AS c ON c.ID = ac.CurrencyID ";
            $SQL .= "WHERE c.ISO IS NOT NULL ";
            if($ID > 0) {
                $SQL .= "AND a.ID = '".$this->db->real_escape_string($ID)."' ";
            }
            $SQL .= "GROUP BY a.ID, ac.CurrencyID ";
            $SQL .= "ORDER BY FIELD(a.Type,'A','L','I','E','N'), a.Code, c.ISO ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
            }

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][$aRow["ID"]]["ID"]   = $aRow["ID"];
                $aReturn["Data"][$aRow["ID"]]["Type"] = $aRow["Type"];
                $aReturn["Data"][$aRow["ID"]]["Name"] = $aRow["Name"];
                $aReturn["Data"][$aRow["ID"]]["Code"] = $aRow["Code"];
                $aReturn["Data"][$aRow["ID"]]["Balance"][$aRow["Currency"]]["SumDebit"]  = $aRow["SumDebit"];
                $aReturn["Data"][$aRow["ID"]]["Balance"][$aRow["Currency"]]["SumCredit"] = $aRow["SumCredit"];
                $aReturn["Columns"]["Balance"][$aRow["Currency"]] = $aRow["Factor"];
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

       /**
        *  Saves an accounting group to the accounting and accounting_group tables
        * =========================================================================
        *  - If $groupID is 0, a new group is created and records inserted.
        *  - If $groupID is larger than 0, the lines related to that ID are deleted and new lines
        *    are inserted.
        *  - Optional, $srcDataID labels one line as the source of the line if the line is related
        *    to for instance a bank transaction. The ID refers to which element this is in the
        *    $aData array, not the ID in the table. The record in table $srcTable with ID $srcRecID
        *    is updated to point back to this line.
        */

        public function saveGroup($groupID, $recDate, $aData, $srcDataID=null, $srcTable="", $srcRecID=null) {

            $groupID  = intval($groupID);
            $recDate  = intval($recDate);
            $updateID = $groupID > 0 ? $groupID : 0;

            $SQL = "";
            if($updateID == 0) {
                $SQL .= "INSERT INTO accounting_group (";
                $SQL .=     "RecordDate, ";
                $SQL .=     "Created ";
                $SQL .= ") VALUES (";
                $SQL .=     $this->dbWrap($recDate,"date").", ";
                $SQL .=     $this->dbWrap(time(),"datetime");
                $SQL .= ");\n";
                $SQL .= "SELECT LAST_INSERT_ID() INTO @GroupID;\n";
                foreach($aData as $iKey=>$aRow) {
                    $bSource = $srcDataID !== null && $srcDataID == $iKey;
                    $SQL .= "INSERT INTO accounting (";
                    $SQL .=     "GroupID, ";
                    $SQL .=     "AccountID, ";
                    $SQL .=     "IsSource, ";
                    $SQL .=     "Details, ";
                    $SQL .=     "CurrencyID, ";
                    $SQL .=     "Debit, ";
                    $SQL .=     "Credit ";
                    $SQL .= ") VALUES (";
                    $SQL .=     "@GroupID, ";
                    $SQL .=     $this->dbWrap($aRow["AccountID"],"int").", ";
                    $SQL .=     $this->dbWrap($bSource,"bool").", ";
                    $SQL .=     $this->dbWrap($aRow["Details"],"text").", ";
                    $SQL .=     $this->dbWrap($aRow["CurrencyID"],"int").", ";
                    $SQL .=     $this->dbWrap(abs($aRow["Debit"]),"int").", ";
                    $SQL .=     $this->dbWrap(abs($aRow["Credit"]),"int")." ";
                    $SQL .= ");\n";
                    if($bSource && $srcTable != "" && $srcRecID !== null) {
                        $SQL .= "SELECT LAST_INSERT_ID() INTO @ReferenceID;\n";
                        $SQL .= "UPDATE ".$srcTable." SET ";
                        $SQL .= "AccountingID = @ReferenceID ";
                        $SQL .= "WHERE ID = ".$this->dbWrap($srcRecID,"int").";\n";
                    }
                }
            }
            // foreach($aData as $iKey=>$aRow) {
            //
            //     $updateID = array_key_exists("ID",$aRow) ? $aRow["ID"] : 0;
            //
            //     if($updateID > 0) {
            //         $SQL .= "UPDATE accounting SET ";
            //         $SQL .= "GroupID = "   .$this->dbWrap($aRow["GroupID"],"int").", ";
            //         $SQL .= "AccountID = " .$this->dbWrap($aRow["AccountID"],"int").", ";
            //         $SQL .= "Details = "   .$this->dbWrap($aRow["Details"],"text").", ";
            //         $SQL .= "CurrencyID = ".$this->dbWrap($aRow["CurrencyID"],"int").", ";
            //         $SQL .= "Debit = "     .$this->dbWrap($aRow["Debit"],"int").", ";
            //         $SQL .= "Credit = "    .$this->dbWrap($aRow["Credit"],"int").", ";
            //         $SQL .= "Updated = "   .$this->dbWrap(time(),"datetime")." ";
            //         $SQL .= "WHERE ID = "  .$this->dbWrap($aRow["ID"],"int").";\n";
            //     } else {
            //         $SQL .= "INSERT INTO accounts (";
            //         $SQL .= "GroupID, ";
            //         $SQL .= "AccountID, ";
            //         $SQL .= "Details, ";
            //         $SQL .= "CurrencyID, ";
            //         $SQL .= "Debit, ";
            //         $SQL .= "Credit, ";
            //         $SQL .= "Created ";
            //         $SQL .= ") VALUES (";
            //         $SQL .= $this->dbWrap($aRow["GroupID"],"int").", ";
            //         $SQL .= $this->dbWrap($aRow["AccountID"],"int").", ";
            //         $SQL .= $this->dbWrap($aRow["Details"],"text").", ";
            //         $SQL .= $this->dbWrap($aRow["CurrencyID"],"int").", ";
            //         $SQL .= $this->dbWrap($aRow["Debit"],"int").", ";
            //         $SQL .= $this->dbWrap($aRow["Credit"],"int").", ";
            //         $SQL .= $this->dbWrap(time(),"datetime").");\n";
            //     }
            // }
            if($SQL == "") return true;

            $oRes = $this->db->multi_query($SQL);
            while($this->db->more_results()) $this->db->next_result();

            if(!$oRes) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
                return false;
            } else {
                return true;
            }
        }
    }
?>
