#!/bin/bash
source="/var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/ipsec_secrets.add"
cat $source >/etc/ipsec.secrets
sleep 5
systemctl restart strongswan-starter
