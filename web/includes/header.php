<?php
   /**
    *  Where's My Money? – Header File
    * =================================
    *  Created 2017-05-30
    */

    if(!isset($bMain)) exit();
?><!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="css/normalize.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/styles.css" type="text/css" media="all">
    <link href="https://fonts.googleapis.com/css?family=Source+Code+Pro" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Berkshire+Swash" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <link rel="icon" href="/icon.png">
    <meta charset="utf-8">
    <title>Where's My Money?</title>
</head>

<body>
    <!-- Begin Left -->
    <div id="left-side">
        <div id="site-logo">
            <span id="logo-wheres">Where's</span>
            <span id="logo-my">my</span>
            <span id="logo-money">money?</span>
        </div>
        <nav id="left-menu">
            <ul>
                <li><span class="menu-icon"><img src="images/icon_overview.png" /></span><a href="index.php">Overview</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_accounts.png" /></span><a href="accounts.php">Accounts</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_bank.png" /></span><a href="funds.php">Funds</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_invest.png" /></span><a href="investments.php">Investments</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_debt.png" /></span><a href="debt.php">Debt</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_income.png" /></span><a href="income.php">Income</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_report.png" /></span><a href="reports.php">Reports</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_tools.png" /></span><a href="tools.php">Tools</a><span class="triangle">&#9658;</span></li>
                <li><span class="menu-icon"><img src="images/icon_settings.png" /></span><a href="settings.php">Settings</a><span class="triangle">&#9658;</span></li>
            </ul>
        </nav>
    </div>
    <!-- End Left -->

    <!-- Begin Content -->
    <div id="site-content">
