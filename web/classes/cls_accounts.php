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

            $SQL  = "";
            $oData = $this->db->query($SQL);

            while($aRow = $oData->fetch_assoc()) {
                $aReturn["Data"][] = $aRow;
            }
            $aReturn["Meta"]["Count"] = count($aReturn["Data"]);

            $toc = microtime(true);
            $aReturn["Meta"]["Time"] = $toc-$tic;

            return $aReturn;
        }
    }
?>
