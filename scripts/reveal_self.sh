#!/bin/sh
# Run as root.

. env.sh

pw=`cat authpass.txt | sed -e s:\n::`
curl "$DEMO_SERVER/register_server/$pw/$WS_SERVER/$WS_PORT"
