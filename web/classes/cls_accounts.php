<?php
   /**
    *  Where's My Money? – Accounts Class
    * ====================================
    *  Created 2017-06-08
    */

    class Accounts extends DataBase
    {
        // Constructor
        function __construct($oDB) {
            parent::__construct($oDB);
        }

        // Methods
        public function getData($ID=0) {

            $tic = microtime(true);

            $aReturn = array(
                "Meta" => array(
                    "Content" => "Bank",
                    "Count"   => 0,
                ),
                "Data" => array(),
            );

            $SQL  = "SELECT ";
            $SQL .= "a.ID AS ID, ";
            $SQL .= "a.Name AS AccountName, ";
            $SQL .= "a.Type AS Type, ";
            $SQL .= "a.Code AS Code, ";
            $SQL .= "a.Description AS Description, ";
            $SQL .= "a.ValidFrom AS ValidFrom, ";
            $SQL .= "a.ValidTo AS ValidTo ";
            $SQL .= "FROM accounts AS a ";
            $SQL .= "WHERE a.ID > 0 ";
            if($ID > 0) {
                $SQL .= "AND a.ID = '".$this->db->real_escape_string($ID)."' ";
            }
            $SQL .= "ORDER BY FIELD(a.Type,'A','L','I','E','N'), a.Code ";
            $oData = $this->db->query($SQL);

            if(!$oData) {
                echo "MySQL Query Failed ...<br />";
                echo "Error: ".$this->db->error."<br />";
                echo "The Query was:<br />";
                echo str_replace("\n","<br />",$SQL);
            }

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = array(
                    "ID"          => $aRow["ID"],
                    "AccountName" => $aRow["AccountName"],
                    "Type"        => $aRow["Type"],
                    "Code"        => $aRow["Code"],
                    "Description" => $aRow["Description"],
                    "ValidFrom"   => strtotime($aRow["ValidFrom"]),
                    "ValidTo"     => strtotime($aRow["ValidTo"]),
                );
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }

        public function saveData($aData) {

            $SQL = "";
            foreach($aData as $iKey=>$aRow) {

                $updateID = array_key_exists("ID",$aRow) ? $aRow["ID"] : 0;

                if($updateID > 0) {
                    $SQL .= "UPDATE accounts SET ";
                    $SQL .= "Name = "       .$this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= "Type = "       .$this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= "Code = "       .$this->dbWrap($aRow["Code"],"text").", ";
                    $SQL .= "Description = ".$this->dbWrap($aRow["Description"],"text").", ";
                    $SQL .= "ValidFrom = "  .$this->dbWrap($aRow["ValidFrom"],"date").", ";
                    $SQL .= "ValidTo = "    .$this->dbWrap($aRow["ValidTo"],"date")." ";
                    $SQL .= "WHERE ID = "   .$this->dbWrap($aRow["ID"],"int").";\n";
                } else {
                    $SQL .= "INSERT INTO accounts (";
                    $SQL .= "Name, ";
                    $SQL .= "Type, ";
                    $SQL .= "Code, ";
                    $SQL .= "Description, ";
                    $SQL .= "ValidFrom, ";
                    $SQL .= "ValidTo ";
                    $SQL .= ") VALUES (";
                    $SQL .= $this->dbWrap($aRow["Name"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Type"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Code"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["Description"],"text").", ";
                    $SQL .= $this->dbWrap($aRow["ValidFrom"],"date").", ";
                    $SQL .= $this->dbWrap($aRow["ValidTo"],"date").");\n";
                }
            }
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
