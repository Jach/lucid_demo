#!/bin/bash
# Use this for creating a new user and giving that user their own
# LucidDB and AdminWS.
# Run as root.

set -x

function randpass() {
  CHAR="[:graph:]"
  cat /dev/urandom | tr -cd "$CHAR" | head -c ${1:-32}
  echo
}

let "N=`ls -l /home/ | grep -v ubuntu | wc -l`"
# make a user
useradd -m -G users -s /bin/bash "user$N"
pw=`randpass`
passwd "user$N" <<EOD
$pw
$pw
EOD

source env.sh

su -l "user$N" -c "
echo \"export JAVA_HOME=$JAVA_HOME\" >> .bashrc
export JAVA_HOME=$JAVA_HOME
export N=$N
"'
source .bashrc
cp -r /luciddb .
# apparently we need to "reinstall"
cd luciddb
rm bin/classpath.gen
rm -rf trace
cd install
./install.sh
cd
# change server to be 8034 + N-users
let "LUCID=8034+$N"
echo "alter system set \"serverHttpPort\" = $LUCID;" | ./luciddb/bin/sqllineEngine
# change sa password?

# change WS properties file
cat > luciddb-jdbc.properties <<EOD
jdbc.driver=org.luciddb.jdbc.LucidDbClientDriver
jdbc.url=jdbc:luciddb:http://localhost:$LUCID
jdbc.username=sa
jdbc.password=
EOD

# change WS server to be 8000- N-users, others we do not care about to be 7000-X
cp -r /dynamodb-services .
cd dynamodb-services/conf
let "P=8000-$N"
sed -i -e "s/port=\"8077\"/port=\"$P\"/g" server.xml # http
let "P=4*($N-1) + 1"
let "P=7000-$P"
sed -i -e "s/port=\"8071\"/port=\"$P\"/g" server.xml # shutdown
let "P=$P-1"
sed -i -e "s/port=\"8073\"/port=\"$P\"/g" server.xml # https
let "P=$P-1"
sed -i -e "s/port=\"8072\"/port=\"$P\"/g" server.xml # ajp
let "P=$P-1"
sed -i -e "s/redirectPort=\"8443\"/redirectPort=\"$P\"/g" server.xml # redirect
cd ../webapps
mkdir adminws
cd adminws
unzip ../adminws.war
mv ~/luciddb-jdbc.properties WEB-INF/classes/

# next script should reveal ourself
'
# end of su command

