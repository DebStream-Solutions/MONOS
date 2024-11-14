# MONOS



## MANUAL FOR SNMP MONITORING

SNMP SERVER:

Install SNMP packages:

sudo dnf install net-snmp net-snmp-utils


Edit the configuration file:

sudo nano /etc/snmp/snmpd.conf


Example configuration:

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






Start and enable the SNMP service:

sudo systemctl enable snmpd
sudo systemctl start snmpd




Test the SNMP configuration:

snmpwalk -v2c -c [COMMUNITY] localhost


