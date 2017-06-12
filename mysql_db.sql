#
#  Database Create SQL for MySQL
#

CREATE TABLE `accounting` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `AccountID` int(11) NOT NULL,
  `IsSource` tinyint(1) NOT NULL DEFAULT '0',
  `Details` varchar(255) DEFAULT NULL,
  `CurrencyID` int(11) NOT NULL,
  `Debit` bigint(20) NOT NULL DEFAULT '0',
  `Credit` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `IDX_IDS` (`GroupID`,`AccountID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `accounting_group` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `RecordDate` date DEFAULT NULL,
  `Year` smallint(6) DEFAULT NULL,
  `Month` tinyint(4) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `Updated` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDX_DATE` (`RecordDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `accounts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` varchar(2) NOT NULL,
  `Name` varchar(32) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Code` varchar(4) DEFAULT NULL,
  `ValidFrom` date DEFAULT NULL,
  `ValidTo` date DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `bank` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `crypto_meta` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TransactionID` int(11) DEFAULT NULL,
  `Height` int(11) DEFAULT NULL,
  `Hash` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ_TID` (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `currency` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Country` varchar(45) DEFAULT NULL,
  `Name` varchar(45) DEFAULT NULL,
  `ISO` varchar(3) DEFAULT NULL,
  `Symbol` varchar(3) DEFAULT NULL,
  `Type` varchar(2) DEFAULT NULL,
  `Factor` bigint(20) DEFAULT NULL,
  `RefCurrency` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `euro_exchange` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
  `Rate` float(9,6) DEFAULT NULL,
  `RateDate` date DEFAULT NULL,
  `Acquired` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ_KEYS` (`Date`,`CurrencyID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `funds` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) DEFAULT NULL,
  `AccountNumber` varchar(128) DEFAULT NULL,
  `SwiftIBAN` varchar(100) DEFAULT NULL,
  `Type` varchar(2) DEFAULT NULL,
  `Category` varchar(2) DEFAULT NULL,
  `BankID` int(11) DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
  `AccountID` int(11) DEFAULT NULL,
  `Opened` date DEFAULT NULL,
  `Closed` date DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Value` varchar(255) DEFAULT NULL,
  `Altered` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ_KEYS` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transactions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FundsID` int(11) NOT NULL,
  `RecordDate` date NOT NULL,
  `TransactionDate` date DEFAULT NULL,
  `Details` varchar(255) DEFAULT NULL,
  `Original` bigint(20) DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
  `Amount` bigint(20) NOT NULL,
  `AccountingID` int(11) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
  `Locked` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `IDX_FUNDS` (`FundsID`),
  KEY `IDX_DATE` (`RecordDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `transactions_yearly` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FundsID` int(11) DEFAULT NULL,
  `RecordDate` date DEFAULT NULL,
  `Amount` bigint(20) DEFAULT NULL,
  `Updated` datetime DEFAULT NULL,
  `Locked` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UNQ_KEYS` (`FundsID`,`RecordDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
