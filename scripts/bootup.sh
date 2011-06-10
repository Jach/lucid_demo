#!/bin/bash
# On a fresh bot from image this script should be run as root.
set -x

# Update LucidDB, AdminWS
./update_adminws.sh
./update_luciddb.sh
# Make X users and announce them
for i in {1..2}; do
  ./new_instance.sh
done
# Start any monitoring daemons
