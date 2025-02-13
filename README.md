# MONOS
*See for free*

**M O N**itoring **O**pen-source **S**ystem


*MONOS* is a free software which allows you to monitor devices in your network. Monitored devices are shown on a web application.

# MANUAL FOR SNMP MONITORING

<details>
  <summary>SNMP SERVER</summary>
  <a href="#snmp-server">SNMP Server</a>  |  <a href="#snmp-client">SNMP Client</a>
</details>
<details>
  <summary>WEB SERVER, PHP SERVER</summary>
  <a href="#apache">Apache Server</a>  |  <a href="#php">PHP Server</a>
</details>
<details>
  <summary>DATABASE</summary>
  <a href="#db">MariaDB</a>  |  <a href="#db-setup">Database Set Up</a>
</details>

## Install Monos on your server

### Install dockerfile
```sh
TODO
```

### Build the docker container
```sh
docker build -t <project-name>
```

### Run the docker
Find docker name:
```sh
docker ps
```
Run the docker
```sh
docker run -it -p 80:80 <container-id>
```


### To run commands in container
Find docker name:
```sh
docker ps
```

Enter the docker
```sh
docker exec -it <container-id> bash
```



## Setup Debian Server for MONOS

### Install required dependencies
```sh
sudo apt install -y snmp snmpd libsnmp-dev snmp-mibs-downloader php-snmp php php-mysqli apache2 libapache2-mod-php mariadb-server 
```

### Install MIBs for SNMP
```sh
sudo download-mibs
```

### Edit configuration of SNMP (snmpd.conf)
```sh
nano /etc/snmp/snmpd.conf
```
Content:
```sh
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
access [COMMUNITY] "" any noauth exact systemview none none

# Logging
dontLogTCPWrappersConnects yes
```

### Enable `mysqli` extension
Locate `php.ini` file
```sh
find / | grep php.ini
```
Edit the file
```sh
nano /etc/php/<version>/apache2/php.ini
```
Enable the extension by adding or uncommenting:
```sh
extension=mysqli
```

### Install MONOS Aplication
Navigate to `/var/www/html/` directory:
```sh
cd /var/www/html/
```
Download the Monos App using `wget` or `git`
```sh
wget https://monos.debstream.org/app/download
```
```sh
git clone https://github.com/DebStream-Solutions/monos.git
# git clone https://username:<pat>@github.com/<your account or organization>/<repo>.git
```

### Configure Monos database

Navigate to directory MONOS
```sh
cd /var/www/html/MONOS
```

Run `db-setup.sh` script to configure database
```sh
sudo /.db-setup.sh
```
Enter your **root** password

| Login to Monos with username `admin` and the admin password <br>
| Login to database with username `mroot` and the admin password


Restart services
```sh
sudo systemctl restart apache2
sudo systemctl restart mariadb
sudo systemctl restart snmpd
```

Now everything is set up on your server and you can prepare the clients


## Client setup - Server & Workstation

