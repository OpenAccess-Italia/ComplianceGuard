#!/bin/bash

LTIMEv4=`stat -c %Z /opt/src/ipv4.txt`
LTIMEv6=`stat -c %Z /opt/src/ipv6.txt`

while true    
do
   ATIMEv4=`stat -c %Z /opt/src/ipv4.txt`
   ATIMEv6=`stat -c %Z /opt/src/ipv6.txt`

   if [[ "$ATIMEv4" != "$LTIMEv4" || "$ATIMEv6" != "$LTIMEv6" ]]
   then    
       /opt/src/push_net46.sh
       LTIMEv4=$ATIMEv4
       LTIMEv6=$ATIMEv6
   fi
   sleep 5
done
