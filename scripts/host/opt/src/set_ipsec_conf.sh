#!/bin/bash
source="/var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/ipsec_conf.add"
cat $source >/etc/ipsec.conf
