# MONOS
*See for free*

**M O N**itoring **O**pen-source **S**ystem


*MONOS* is a free software which allows you to monitor devices in your network. Monitored devices are shown on a web application.

# MANUAL FOR SNMP MONITORING

<nav> SETTING UP SNMP:
  <a href="#snmp-server">SNMP SERVER</a>  |  <a href="#snmp-client">SNMP CLIENT</a>
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
