#!/bin/bash
# On a fresh bot from image this script should be run as root.
set -x

# Update LucidDB, AdminWS
exec ./update_adminws.sh
exec ./update_luciddb.sh
# Make X users and announce them
for i in {1..2}; do
  exec new_instace.sh
done
# Start any monitoring daemons
