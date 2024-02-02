#!/bin/bash
source="/var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/network.csv"
IF_NAME=`ip route get 8.8.8.8 | awk -- '{printf $5}'`
HEAD="auto lo
iface lo inet loopback
auto $IF_NAME
iface $IF_NAME inet static"
echo "$HEAD" > /etc/network/interfaces
while IFS= read -r -d$'\n' line
do
out=$(echo "$line" |sed "s/IP,/address /g"|sed "s/MASK,/netmask /g"|sed "s/GW,/gateway /g")
echo "$out"
done <"$source" >>/etc/network/interfaces
echo dns-nameservers 1.1.1.1 8.8.8.8 >>/etc/network/interfaces
#echo
#echo "Restarting network"
#systemctl restart networking.service
#echo "Done"
