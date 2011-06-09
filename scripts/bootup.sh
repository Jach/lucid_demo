#!/bin/sh
# On a fresh boot from image this script should be run as root.

# Update LucidDB, AdminWS
exec ./update_adminws.sh
exec ./update_luciddb.sh
# Make X users and announce them
# Start any monitoring daemons
