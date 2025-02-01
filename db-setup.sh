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
    echo -n "Enter your root password: "
    read -s password
    echo

    # Validate the password
    #if validate_password "$password"; then
    #    echo "Password is secure!"
    #    echo "HASH-BASH: $password"
    #    break
    #else
    #    echo "Please try again."
    #fi

    echo "$password" | sudo -S echo > /dev/null 2>&1

    # Check if the password was correct
    if [ $? -eq 0 ]; then
        echo "Password is correct!"
        break
    else
        echo "Incorrect password."
    fi

done

# Database credentials
DB_USER="root"
DB_PASS=$password
DB_NAME="monos"


GENERATED_PASS=$(head /dev/urandom | tr -dc 'A-Za-z0-9!@#$%^&*()_+{}|:<>?' | head -c 32)

# Generate or edit config_file
config_file="db_config.php"

config_host='localhost'
config_user='mroot'
config_pass='heslo'
config_name=$DB_NAME

content=$(cat <<EOF
<?php
return [
    'db_host' => '$config_host',
    'db_user' => '$config_user',
    'db_pass' => '$config_pass',
    'db_name' => '$config_name',
];
EOF
)

echo "$content" > "$config_file"
chmod 777 "$config_file"


# Creates database "monos" and primary user

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


CREATE USER IF NOT EXISTS '$config_user'@'%' IDENTIFIED BY '$config_pass';
GRANT ALL PRIVILEGES ON $config_name.* TO '$config_user'@'%';
FLUSH PRIVILEGES;


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

php workspace/action/validate.php "adminpass_#Ad5f78:$config_pass"

echo ""
echo "Your temporary admin password is: $config_pass"

systemctl restart mariadb
systemctl restart apache2