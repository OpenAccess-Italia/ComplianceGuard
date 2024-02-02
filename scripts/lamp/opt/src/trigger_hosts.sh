#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /var/www/html/storage/settings/hosts.add`

while true    
do
   ATIME=`stat -c %Z /var/www/html/storage/settings/hosts.add`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/set_hosts.sh
       LTIME=$ATIME
   fi
   sleep 5
done

