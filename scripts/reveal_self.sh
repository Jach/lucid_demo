#!/bin/bash
# Run as root. Expects USER and WS_PORT to be passed in the env.

. env.sh

su -l $USER -c "export JAVA_HOME=$JAVA_HOME
cd luciddb/bin/
./lucidDbServer &
cd ~/dynamodb-services
chmod +x ./bin/*.sh
./bin/startup.sh
"
sleep 60 # give time for things to start up
WS_SERVER=`ec2metadata | grep public-hostname | sed -e "s/^.* //"`
pw=`cat authpass.txt`
sapass=`cat sapass`
sapass=`python -c "import urllib2; print(urllib2.quote('''$sapass''', safe=''))"`
curl "$DEMO_SERVER/register_server/$pw/$WS_SERVER/$WS_PORT/$sapass"
