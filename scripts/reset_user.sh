#!/bin/bash
# Run as root. Does the same thing as bootup but without the useradd.

# Expects WS_PORT to be in the env, so we can determine user#.

let USER_N="8000 - $WS_PORT"

USER_N=$USER_N ./new_instance.sh
WS_PORT=$WS_PORT USER=user$USER_N ./reveal_self.sh
chmod -R o-wrx /home/user$i
