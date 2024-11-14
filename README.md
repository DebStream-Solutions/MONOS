# MONOS
*See for free*

**M O N**itoring **O**pen-source **S**ystem


*MONOS* is a free software which allows you to monitor devices in your network. Monitored devices are shown on a web application.

# MANUAL FOR SNMP MONITORING


<nav>
  <a href="#snmp-server">SNMP SERVER</a>  |  <a href="#snmp-client">SNMP CLIENT</a>
</nav>
<br>
<nav>
  <a href="#web-server">WEB SERVER</a>
</nav>
<br>
<nav>
  <a href="#db">DATABASE</a>
</nav>

## <a name="snmp-server"> SNMP SERVER: </a>

### Install SNMP packages:
```sh
sudo dnf install net-snmp net-snmp-utils
```

### Edit the configuration file:
```sh
sudo nano /etc/snmp/snmpd.conf
```

### Example configuration:
```sh
# Update [COMMUNITY] with your preferred string
rwcommunity [COMMUNITY] default

# Disk monitoring
disk  / 100

# Agent user
agentuser  [USER]

# Agent address
agentAddress udp:161

# System location and contact
syslocation Unknown
syscontact Root <root@localhost>

# Access control
access  [COMMUNITY] "" any noauth exact systemview none none

# Logging
dontLogTCPWrappersConnects yes
```

### Start and enable the SNMP service:
```sh
sudo systemctl enable snmpd
sudo systemctl start snmpd
```

### Test the SNMP configuration:
```sh
snmpwalk -v2c -c [COMMUNITY] localhost
```

## <a name="snmp-client"> SNMP CLIENT: </a>

### Install SNMP packages:
```sh
sudo apt update
sudo apt install snmpd
```


### Edit the SNMP configuration file:
```sh
sudo nano /etc/snmp/snmpd.conf
```



### Example configuration:
```sh
# Update [COMMUNITY] with your preferred string
rocommunity [COMMUNITY] default

# Disk monitoring
disk  / 100

# System location and contact
syslocation Home
syscontact Admin <admin@localhost>

# Access control
access  [COMMUNITY] "" any noauth exact systemview none none

# Agent address
agentAddress udp:161,udp6:[::1]:161
```


### Edit the default SNMP settings:
```sh
sudo nano /etc/default/snmpd
```

**Change the line:**
```sh
SNMPDOPTS='-Lsd -Lf /dev/null -p /run/snmpd.pid -a'
```

**TO:**
```sh
SNMPDOPTS='-Lsd -Lf /dev/null -p /run/snmpd.pid -a -x tcp:localhost:161'
```


### Restart the SNMP service:

sudo systemctl restart snmpd



### Test the SNMP configuration:
```sh
snmpwalk -v2c -c [COMMUNITY] localhost
```


## WEB SERVER & PHP SERVER


### 1. Update System Packages
First, ensure your system packages are up to date:
```sh
sudo dnf update
```

### 2. Install Apache
Apache is the web server that will serve your PHP files.
```sh
sudo dnf install httpd
sudo systemctl enable httpd
sudo systemctl start httpd
```

### 3. Install PHP
Next, install PHP and the necessary modules:
```sh
sudo dnf install php php-common php-mysqlnd php-snmp #php-gd php-xml php-mbstring php-json php-curl
```

### 4. Install MariaDB (Optional)
If you need a database, install MariaDB:
```sh
sudo dnf install mariadb-server
sudo systemctl enable mariadb
sudo systemctl start mariadb
```

### 5. Configure Firewall
Allow HTTP and HTTPS traffic through the firewall:
```sh
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

### 6. Create a PHP File
Create an index.php file in the web server’s root directory:
```sh
sudo nano /var/www/html/index.php
```


### Open the Apache Configuration File:
```sh
sudo nano /etc/httpd/conf/httpd.conf
```


### Add the DirectoryIndex Directive:
Locate the DirectoryIndex directive and modify it as follows:
```sh
DirectoryIndex index.php index.html
```


### File Permissions
Set file permissions for /html to ensure all files and directories are accessible.
```sh
sudo chmod -R 755 /var/www/html
```


### Configure Apache to Use PHP
Ensure that Apache is configured to handle PHP files. Open the Apache configuration file:
```sh
sudo nano /etc/httpd/conf/httpd.conf
```


### Add or ensure the following lines are present:
```sh
#LoadModule php_module modules/libphp.so
AddHandler php-script .php
```


Add these lines to display php files properly:
```sh
<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>
```


### Use PHP-FPM (Optional)
If problems appear this might help.
```sh
sudo systemctl enable php-fmp
sudo systemctl start php-fpm
```


### Restart Apache:
Restart the Apache server to apply the changes.
```sh
sudo systemctl restart httpd
```


How to CURL tar.gz?
```sh
curl http://mat.whistlers.site/monos.tar.gz -o - | tar -xz -C /var/www/html/
```



## PHP AND SNMP

### Enable SNMP in PHP

First look for php.ini file, it should be in /etc/php.ini or you can look for it using this command:
```sh
php –ini
```


Now enable SNMP in PHP by editing the php.ini file. Add the following line:
```sh
extension=snmp
```


### Restart Apache:
Restart the Apache server to apply the changes.
```sh
sudo systemctl restart httpd
```

