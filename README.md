# MONOS
*See for free*
**M O N**itoring **O**pen-source **S**ystem

*MONOS* is a 

# MANUAL FOR SNMP MONITORING

## SNMP SERVER:

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

