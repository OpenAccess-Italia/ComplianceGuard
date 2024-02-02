#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /opt/src/ipv6.txt`

while true    
do
   ATIME=`stat -c %Z /opt/src/ipv6.txt`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/push_net6.sh
       LTIME=$ATIME
   fi
   sleep 5
done
