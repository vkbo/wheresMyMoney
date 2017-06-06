<?php
   /**
    *  Where's My Money? – Index File
    * ================================
    *  Created 2017-05-30
    */

    $bMain  = true;
    require_once("includes/init.php");
    $theOpt = new Settings($oDB);

    /**
     *  Page Content
     */

     // Page Header
     require_once("includes/header.php");
?>
     <!-- Begin Header -->
     <header id="header"><h1>Overview</h1></header>
     <nav id="main-menu">
         <b>Options:</b>
         <ul>
             <?php
                 echo "<li><a href='index.php'>Main</a></li>";
             ?>
         </ul>
     </nav>
     <!-- End Header -->
<?php
     require_once("includes/footer.php");
?>
