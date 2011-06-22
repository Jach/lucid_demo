#!/bin/bash
# On a fresh bot from image this script should be run as root.
set -x

# Update LucidDB, AdminWS
./update_adminws.sh
./update_luciddb.sh
# Make X users and announce them
for i in {1..4}; do
  ./new_instance.sh
  let WS_PORT="8000 - $i"
  WS_PORT=$WS_PORT USER=user$i ./reveal_self.sh
  chmod -R o-wrx /home/user$i
done
# Start any monitoring daemons
