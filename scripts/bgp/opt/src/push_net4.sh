#!/bin/sh
cat /opt/src/ipv4.txt|while read ip;do bgpctl network add $ip/32 localpref 120 community NO_EXPORT community BLACKHOLE; done
