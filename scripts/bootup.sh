#!/bin/sh
# On a fresh boot from image this script should be run as root.

# Update LucidDB, AdminWS
exec ./update_adminws.sh
exec ./update_luciddb.sh
# Make X users and announce them
for i in {1..2}; do
  exec new_instace.sh
done
# Start any monitoring daemons
