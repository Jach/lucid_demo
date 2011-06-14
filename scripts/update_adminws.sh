#!/bin/bash
# Use this for updating the common adminWS with tomcat.

source env.sh

cd $ROOT_DIR

wget http://build.dynamobi.com/job/dynamo_services/lastSuccessfulBuild/artifact/dynamodb-services/deploy/dynamobi-services.zip
unzip -o dynamobi-services.zip
rm -f dynamobi-services.zip
cd dynamodb-services
rm -rf webapps/ROOT/* webapps/adminui.war
cat > webapps/ROOT/crossdomain.xml <<EOD
<?xml version="1.0" ?>
<cross-domain-policy>
  <site-control permitted-cross-domain-policies="master-only" />
  <allow-access-from domain="demo.dynamobi.com" />
  <allow-http-request-headers-from domain="demo.dynamobi.com" headers="*" />
</cross-domain-policy>
EOD
cd ..
rm -rf /dynamodb-services
cp -r dynamodb-services /
rm -rf dynamodb-services
chmod -R +r /dynamodb-services
