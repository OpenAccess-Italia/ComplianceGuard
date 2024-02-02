#!/bin/bash
iptables -F
iptables -t nat -F
source="/var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/iptables.add"
IF_NAME=`ip route get 8.8.8.8 | awk -- '{printf $5}'`
FILTER="*filter
:INPUT ACCEPT [0:0]
:FORWARD ACCEPT [0:0]
:OUTPUT ACCEPT [1484:171984]
-A INPUT -p icmp -m icmp --icmp-type 8 -m limit --limit 30/min --limit-burst 120 -j ACCEPT
-A INPUT -i lo -j ACCEPT
-A INPUT -i lxcbr0 -j ACCEPT
-A INPUT -p tcp -m tcp --dport 55022 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
-A INPUT -p tcp -m tcp --dport 55080 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
-A INPUT -p tcp -m tcp --dport 179 -m conntrack --ctstate NEW,ESTABLISHED -j ACCEPT
-A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT
-A INPUT -m conntrack --ctstate INVALID -j DROP
-A INPUT -j REJECT --reject-with icmp-port-unreachable
-A OUTPUT -o lo -j ACCEPT
-A OUTPUT -o lxcbr0 -j ACCEPT
COMMIT"
NAT="*nat
:PREROUTING ACCEPT [41:9618]
:INPUT ACCEPT [1:84]
:OUTPUT ACCEPT [1:76]
:POSTROUTING ACCEPT [1:76]
-A PREROUTING -p tcp -m tcp --dport 55080 -j DNAT --to-destination 10.0.3.245:80
-A PREROUTING -p tcp -m tcp --dport 179 -j DNAT --to-destination 10.0.3.125:179
COMMIT"
echo "$FILTER" > /etc/iptables/rules.v4
echo "$NAT" >> /etc/iptables/rules.v4
iptables-restore</etc/iptables/rules.v4
sh $source
#systemctl restart iptables
