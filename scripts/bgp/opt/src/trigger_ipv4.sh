#!/bin/bash

### Set initial time of file
LTIME=`stat -c %Z /opt/src/ipv4.txt`

while true    
do
   ATIME=`stat -c %Z /opt/src/ipv4.txt`

   if [[ "$ATIME" != "$LTIME" ]]
   then    
       /opt/src/push_net4.sh
       LTIME=$ATIME
   fi
   sleep 5
done
