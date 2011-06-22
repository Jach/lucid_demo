#!/bin/bash
# helper script to wipe out any active users

for u in `ls /home`; do
  if [ "$u" != 'ubuntu' ]; then
    killall -9 -u $u # make sure everything is dead
    userdel -r $u
  fi
done
