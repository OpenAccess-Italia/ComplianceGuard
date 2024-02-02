#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/bgp.csv`

while true    
do
   ATIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/bgp.csv`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/set_bgpd.sh
       LTIME=$ATIME
   fi
   sleep 5
done
