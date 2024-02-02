#!/bin/bash
source="/var/www/html/storage/settings/hosts.add"
HEAD="127.0.1.1	lamp
127.0.0.1	localhost
::1		localhost ip6-localhost ip6-loopback
ff02::1		ip6-allnodes
ff02::2		ip6-allrouters"
echo "$HEAD" > /etc/hosts
cat $source >>/etc/hosts
