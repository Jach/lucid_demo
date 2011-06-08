#!/bin/sh
# Use this for updating and populating LucidDB.
# Run as root.

. env.sh
pwd=`pwd`

cd ROOT_DIR
wget http://build.dynamobi.com/job/dy_dev_initbuild/label=lin64/lastSuccessfulBuild/artifact/luciddb/dist/luciddb.tar.bz2
tar jxf luciddb.tar.bz2
mv luciddb-0.0.0 luciddb
cd luciddb/install
./install.sh
cd ROOT_DIR
cat $pwd/load_data.sql | ./luciddb/bin/sqlineEngine
cp -r -f luciddb /
chmod -R +r /luciddb
