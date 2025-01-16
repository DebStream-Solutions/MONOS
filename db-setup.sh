#!/bin/bash

# Database credentials
DB_USER="root"
DB_PASS="abcdef"
DB_NAME="monos"

# Connect to MySQL and create the database
mysql -u $DB_USER -p$DB_PASS <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
USE $DB_NAME;


-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hash VARCHAR(255)
);


-- Create profiles table
CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

-- Create devices table
CREATE TABLE IF NOT EXISTS devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type INT,
    name VARCHAR(255),
    ip VARCHAR(15)
);

-- Create profileReleations table
CREATE TABLE IF NOT EXISTS profileReleations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profileId INT,
    deviceId INT
);

--Create networkTraffic table
CREATE TABLE IF NOT EXISTS networkTraffic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp DATETIME NOT NULL,
    upload BIGINT NOT NULL,
    download BIGINT NOT NULL
);

-- Create templates table
CREATE TABLE IF NOT EXISTS templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    typeId INT,
    name VARCHAR(255)
);

-- Create oids table
CREATE TABLE IF NOT EXISTS oids (
    templateId INT,
    id INT AUTO_INCREMENT PRIMARY KEY,
    oid VARCHAR(255),
    name VARCHAR(255)
);

-- Insert data into oids table
INSERT INTO oids (templateId, id, oid, name) VALUES
(1, 1, '1.3.2.1.6.1.1.0', 'cpu'),
(1, 2, '1.3.2.1.6.1.1.0', 'gpu'),
(1, 3, '1.3.2.1.6.1.1.0', 'disk'),
(1, 4, '1.3.2.1.6.1.1.0', 'users'),
(2, 5, '1.3.2.1.6.1.1.0', 'network'),
(2, 6, '1.3.2.1.6.1.1.0', 'processes'),
(2, 7, '1.3.2.1.6.1.1.0', 'ram'),
(2, 8, '1.3.2.1.6.1.1.0', 'last-log'),
(2, 9, '1.3.2.1.6.1.1.0', 'power-usage'),
(2, 10, '1.3.2.1.6.1.1.0', 'user-name');

-- Create types table
CREATE TABLE IF NOT EXISTS types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);

-- Insert data into types table
INSERT INTO types (id, name) VALUES
(1, 'router'),
(2, 'switch'),
(3, 'workstation'),
(4, 'server'),
(5, 'printer'),
(6, 'firewall'),
(7, 'load-balancer'),
(8, 'hub'),
(9, 'camera'),
(10, 'ip-telephone'),
(11, 'cable-modem');
EOF

echo "Database and tables created successfully."
