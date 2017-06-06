#
#  Database Create SQL for MySQL
#

CREATE TABLE `account` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` tinyint(1) DEFAULT NULL,
  `Name` varchar(15) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `Code` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `accounting` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TransactionID` int(11) DEFAULT NULL,
  `AccountID` int(11) DEFAULT NULL,
  `Amount` bigint(20) DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
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
  `Type` varchar(2) DEFAULT NULL,
  `Category` varchar(2) DEFAULT NULL,
  `BankID` int(11) DEFAULT NULL,
  `CurrencyID` int(11) DEFAULT NULL,
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
  `Complete` tinyint(1) DEFAULT NULL,
  `Created` datetime DEFAULT NULL,
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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
