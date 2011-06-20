#!/bin/bash
# helper script to wipe out any active users
let "N=`ls -l /home/ | grep -v ubuntu | wc -l`"
let "N=N-1"

for i in {1..$N}; do
  killall -u user$i
  userdel -r user$i
done
