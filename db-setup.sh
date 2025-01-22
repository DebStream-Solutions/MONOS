#!/bin/bash

#!/bin/bash

# Function to validate password security
validate_password() {
    local password=$1

    # Check if the password length is at least 8 characters
    if [[ ${#password} -lt 8 ]]; then
        echo "Password must be at least 8 characters long."
        return 1
    fi

    # Check for at least one uppercase letter
    if ! [[ $password =~ [A-Z] ]]; then
        echo "Password must contain at least one uppercase letter."
        return 1
    fi

    # Check for at least one lowercase letter
    if ! [[ $password =~ [a-z] ]]; then
        echo "Password must contain at least one lowercase letter."
        return 1
    fi

    # Check for at least one number
    if ! [[ $password =~ [0-9] ]]; then
        echo "Password must contain at least one number."
        return 1
    fi

    # Check for at least one special character
    if ! [[ $password =~ [[:punct:]] ]]; then
        echo "Password must contain at least one special character (e.g., @, #, $, etc.)."
        return 1
    fi

    # If all checks pass, return success
    return 0
}

# Main script to prompt for a secure password
while true; do
    echo -n "Enter secure admin password: "
    read -s password
    echo

    # Validate the password
    if validate_password "$password"; then
        echo "Password is secure!"
        break
    else
        echo "Please try again."
    fi
done

# Optional: Use or pass the secure password to another script
hashed_password=$(php workspace/admin-pass.php "adminpass_#Ad5f78:$password")
echo "Hashed Password: $hashed_password"


# Database credentials
DB_USER="root"
DB_PASS=$password
DB_NAME="monos"

# Connect to MySQL and create the database
mysql -u $DB_USER -p$DB_PASS <<EOF
CREATE DATABASE IF NOT EXISTS $DB_NAME;
USE $DB_NAME;


CREATE USER '$DB_USER'@'%' IDENTIFIED BY 'y%8YB@*T$@7dTPhCfhge9xNJ9fxTvEmYs8sSzrJ6';
GRANT INSERT, UPDATE, DELETE ON $DB_NAME.* TO '$DB_USER'@'%';
FLUSH PRIVILEGES;


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
