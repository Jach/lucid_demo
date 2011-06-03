#!/bin/sh
# Use this for updating the adminUI served by PHP.

cd ../phpserver/html
mkdir flex
cd flex
wget http://build.dynamobi.com/job/dynamo_admin/lastSuccessfulBuild/artifact/flexsqladmin/adminui.war
unzip -o adminui.war
rm adminui.war
rm -rf META-INF
rm index.html
mv SQLAdmin.html ../../app/templates/
cd ../../app/templates/
patch SQLAdmin.html < ui.patch
mv SQLAdmin.html SQLAdmin.tpl
