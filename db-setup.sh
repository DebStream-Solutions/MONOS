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
    hash VARCHAR(255) NOT NULL
);

-- Create types table
CREATE TABLE IF NOT EXISTS types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);


-- Create profiles table
CREATE TABLE IF NOT EXISTS profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create devices table
CREATE TABLE IF NOT EXISTS devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    ip VARCHAR(45) NOT NULL,
    FOREIGN KEY (type) REFERENCES types(id)
);

-- Create profileReleations table
CREATE TABLE IF NOT EXISTS profileReleations (
    profileId INT NOT NULL,
    deviceId INT NOT NULL,
    PRIMARY KEY (profileId, deviceId),
    FOREIGN KEY (profileId) REFERENCES profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (deviceId) REFERENCES devices(id) ON DELETE CASCADE
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
