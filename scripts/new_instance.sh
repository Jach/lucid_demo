#!/bin/sh
# Use this for creating a new user and giving that user their own
# LucidDB and AdminWS.
# Run as root.

function randpass() {
  CHAR="[:graph:]"
  cat /dev/urandom | tr -cd "$CHAR" | head -c ${1:-32}
  echo
}

let "N=`ls -l /home/ | wc -l`"
# make a user
useradd -m -G users -s /bin/bash "user$N"
pw=`randpass`
passwd "user$N" << EOD
$pw
EOD

. env.sh

su - "user$N"
cp -r /luciddb .
# change server to be 8034 + N-users
let "P=8034+$N-1"
echo "alter system set \"serverHttpPort\" = $P;" | ./luciddb/bin/sqlineEngine
# change sa password?

# change WS server to be 8034 - N-users, others to be 7000 - X
cp -r /dynamodb-services .
cd dynamodb-services/conf
sed -i -e s/port="8077"/port="$P"/g server.xml

# next script should reveal ourself
