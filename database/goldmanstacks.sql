CREATE TABLE users (
  userID int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  userRole enum('client','employee') NOT NULL,
  lastSignin datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  email varchar(254) NOT NULL,
  password varchar(255) NOT NULL,
  firstName varchar(43) NOT NULL,
  middleName varchar(43) DEFAULT NULL,
  lastName varchar(43) NOT NULL,
  phoneNumber varchar(15) NOT NULL,
  ssn varchar(9) NOT NULL,
  CONSTRAINT pk_user PRIMARY KEY (userID)
) ENGINE=InnoDB;

CREATE TABLE employee (
  employeeID int(10) UNSIGNED NOT NULL,
  position varchar(50) NOT NULL,
  permission tinyint(4) NOT NULL,
  salary float(10,2) DEFAULT NULL,
  CONSTRAINT pk_employee PRIMARY KEY (employeeID),
  CONSTRAINT fk_employee FOREIGN KEY (employeeID) REFERENCES users (userID) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE client (
  clientID int(10) UNSIGNED NOT NULL,
  verified tinyint(1) NOT NULL DEFAULT '0',
  birthDate date NOT NULL,
  CONSTRAINT pk_client PRIMARY KEY (clientID),
  CONSTRAINT fk_client FOREIGN KEY (clientID) REFERENCES users (userID) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE address (
  userID int(10) UNSIGNED NOT NULL,
  line1 varchar(50) NOT NULL,
  line2 varchar(50) DEFAULT NULL,
  city varchar(30) NOT NULL,
  state varchar(30) NOT NULL,
  postalCode varchar(11) NOT NULL,
  CONSTRAINT pk_address_user PRIMARY KEY (userID),
  CONSTRAINT fk_address_user FOREIGN KEY (userID) REFERENCES users (userID) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE accountDirectory (
  accountNum int(16) UNSIGNED NOT NULL AUTO_INCREMENT,
  accountType enum('checking','savings','credit') NOT NULL,
  balance float(10,2) NOT NULL DEFAULT '0.00',
  nickName varchar(30) NOT NULL,
  clientID int(10) UNSIGNED NOT NULL,
  CONSTRAINT pk_account_number PRIMARY KEY (accountNum),
  CONSTRAINT fk_account_client FOREIGN KEY (clientID) REFERENCES client (clientID) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT uq_nickname UNIQUE KEY (nickName, clientID)
) ENGINE=InnoDB;

CREATE TABLE accountRequests (
  requestID int(11) NOT NULL AUTO_INCREMENT,
  clientID int(10) UNSIGNED NOT NULL,
  accountType enum('checking','savings','credit') NOT NULL,
  requestDate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_open_request PRIMARY KEY (requestID),
  CONSTRAINT fk_open_client FOREIGN KEY (clientID) REFERENCES client (clientID) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT uq_open_request UNIQUE KEY (clientID, accountType)
) ENGINE=InnoDB;

CREATE TABLE accountCloseRequests (
  accountNum int(16) UNSIGNED NOT NULL,
  requestDate datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT pk_close_account PRIMARY KEY (accountNum),
  CONSTRAINT fk_close_account FOREIGN KEY (accountNum) REFERENCES accountDirectory (accountNum) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE savings (
  accountNum int(16) UNSIGNED NOT NULL,
  interestRate float(3,2) NOT NULL,
  CONSTRAINT pk_savings PRIMARY KEY (accountNum),
  CONSTRAINT fk_savings FOREIGN KEY (accountNum) REFERENCES accountDirectory (accountNum) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE credit (
  accountNum int(16) UNSIGNED NOT NULL,
  interestRate float(3,2) NOT NULL,
  lastPayment datetime NOT NULL,
  CONSTRAINT pk_credit PRIMARY KEY (accountNum),
  CONSTRAINT fk_credit FOREIGN KEY (accountNum) REFERENCES accountDirectory (accountNum) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE payments (
  paymentID int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  accountNum int(16) UNSIGNED NOT NULL,
  recipientAccount int(16) UNSIGNED DEFAULT NULL,
  recipientNickName varchar(30) DEFAULT NULL,
  amount float(10,2) NOT NULL,
  paymentDate date NOT NULL,
  step int(11) DEFAULT NULL,
  endDate date DEFAULT NULL,
  CONSTRAINT pk_payment PRIMARY KEY (paymentID),
  CONSTRAINT fk_payment_account FOREIGN KEY (accountNum) REFERENCES accountDirectory (accountNum) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE transactions (
  transactionID int(11) NOT NULL AUTO_INCREMENT,
  transactionTime datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  clientID int(10) UNSIGNED NOT NULL,
  accountNum int(16) UNSIGNED NOT NULL,
  type varchar(20) DEFAULT NULL,
  transactionAmount float(10,2) NOT NULL,
  recipientAccount int(16) UNSIGNED DEFAULT NULL,
  CONSTRAINT pk_transactions PRIMARY KEY (transactionID)
) ENGINE=InnoDB;
