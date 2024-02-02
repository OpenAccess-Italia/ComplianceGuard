#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/download/ipv4.txt`

while true    
do
   ATIME=`stat -c %Z /var/lib/lxc/lamp/rootfs/var/www/html/storage/download/ipv4.txt`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/cp_ipv4.sh
       LTIME=$ATIME
   fi
   sleep 5
done
