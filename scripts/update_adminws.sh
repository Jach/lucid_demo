#!/bin/bash
# Use this for updating the common adminWS with tomcat.
set -x

source env.sh

cd $ROOT_DIR

wget http://build.dynamobi.com/job/dynamo_services/lastSuccessfulBuild/artifact/dynamodb-services/deploy/dynamobi-services.zip
unzip -o dynamobi-services.zip
cd dynamodb-services
rm -rf webapps/ROOT webapps/adminui.war
cd ..
rm -rf /dynamodb-services
cp -r dynamodb-services /
rm -rf dynamodb-services
chmod -R +r /dynamodb-services
