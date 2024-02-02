#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/ipsec_conf.add`

while true    
do
   ATIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/settings/ipsec_conf.add`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/set_ipsec_conf.sh
       LTIME=$ATIME
   fi
   sleep 5
done
