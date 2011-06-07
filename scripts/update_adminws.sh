#!/bin/sh
# Use this for updating the common adminWS with tomcat.

cd /root

wget http://build.dynamobi.com/job/dynamo_services/lastSuccessfulBuild/artifact/dynamodb-services/deploy/dynamobi-services.zip
unzip dynamobi-services.zip
cd dynamodb-services
rm -rf webapps/ROOT webapps/adminui.war
cd ..
cp -r dynamodb-services /
chmod -R +r /dynamodb-services
