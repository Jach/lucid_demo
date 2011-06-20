#!/bin/bash
# helper script to wipe out any active users
let "N=`ls -l /home/ | grep -v ubuntu | wc -l`"
let "N=N-1"

for (( i=1; i<=$N; i++ )); do
  killall -u user$i
  sleep 10
  userdel -r user$i
done
