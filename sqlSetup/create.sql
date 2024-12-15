-- Create the database
CREATE DATABASE IF NOT EXISTS leidson_1;
USE leidson_1;

-- Create State table
CREATE TABLE IF NOT EXISTS State (
    Name VARCHAR(14) PRIMARY KEY,
    Senate_Address VARCHAR(256),
    Senate_Email VARCHAR(256),
    Senate_Website VARCHAR(4000)
);

-- Create Senator table
CREATE TABLE IF NOT EXISTS Senator (
    Name VARCHAR(256) PRIMARY KEY,
    Phone_Number VARCHAR(12),
    Class VARCHAR(3) DEFAULT 'I',
    Terms_Served TINYINT DEFAULT 0,
    Party CHAR(1) DEFAULT 'i',
    State VARCHAR(14) NOT NULL,
    FOREIGN KEY (State) REFERENCES State(Name)
);

-- Create Leadership table
CREATE TABLE IF NOT EXISTS Leadership (
    Senator_Name VARCHAR(256) PRIMARY KEY,
    Type VARCHAR(256) NOT NULL,
    FOREIGN KEY (Senator_Name) REFERENCES Senator(Name)
);

-- Create Committee table
CREATE TABLE IF NOT EXISTS Committee (
    Committee_ID SMALLINT NOT NULL AUTO_INCREMENT,
    Committee_Name VARCHAR(256) NOT NULL UNIQUE,
    Chair_Name VARCHAR(256) NOT NULL,
    FOREIGN KEY (Chair_Name) REFERENCES Senator(Name),
    PRIMARY KEY (Committee_ID)
);
ALTER TABLE Committee AUTO_INCREMENT=1;

-- Create Subcommittee table
CREATE TABLE IF NOT EXISTS Subcommittee (
    Subcommittee_ID SMALLINT PRIMARY KEY,
    Subcommittee_Name VARCHAR(256) NOT NULL,
    Chair_Name VARCHAR(256) NOT NULL,
    Supercommittee_ID SMALLINT NOT NULL,
    FOREIGN KEY (Chair_Name) REFERENCES Senator(Name),
    FOREIGN KEY (Supercommittee_ID) REFERENCES Committee(Committee_ID)
);

-- Create Is_Member table
CREATE TABLE IF NOT EXISTS Is_Member (
    Membership_ID SMALLINT NOT NULL AUTO_INCREMENT,
    Senator_Name VARCHAR(256),
    Committee_ID SMALLINT,
    Subcommittee_ID SMALLINT,
    PRIMARY KEY (Membership_ID),
    FOREIGN KEY (Committee_ID) REFERENCES Committee(Committee_ID),
    FOREIGN KEY (Subcommittee_ID) REFERENCES Subcommittee(Subcommittee_ID),
    FOREIGN KEY (Senator_Name) REFERENCES Senator(Name)
);

-- User table for login
CREATE TABLE IF NOT EXISTS User (
    User_Name VARCHAR(256) PRIMARY KEY,
    Password VARCHAR(256) NOT NULL,
    Is_Admin BOOLEAN DEFAULT 0
);
