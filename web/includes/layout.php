<?php
   /**
    *  Where's My Money? – Layout Functions
    * ======================================
    *  Created 2017-05-31
    */

    function makePageHeader($aParts, $sView) {

        echo "<header id='header'><h1>".$aParts[$sView]["Title"]."</h1></header>\n";
        echo "<div id='main-menu'>\n";
        echo "<b>Options:</b>\n";
        echo "<ul>";
        foreach($aParts as $sKey=>$aValues) {
            if($aValues["Menu"] != "") {
                echo "<li><a href='".$aValues["URL"]."'>".$aValues["Menu"]."</a></li>";
            }
        }
        echo "</ul>\n";
        echo "</div>\n";

        return;
    }

    function echoPagination($thisPage, $pageNum, $nPages) {
        echo "<b>Pages:</b>&nbsp;";
        for($p=1; $p<=$nPages; $p++) {
            if($p == $pageNum) {
                echo "<b>".$p."</b>";
            } else {
                echo "<a href='".$thisPage."&Page=".$p."'>".$p."</a>";
            }
            if($p < $nPages) echo "&nbsp;|&nbsp;";
        }
    }
?>
