#!/bin/sh
cat /opt/src/ipv6.txt|while read ip;do bgpctl network add $ip/128 localpref 120 community NO_EXPORT community BLACKHOLE; done
