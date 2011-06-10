#!/bin/bash
# Use this for updating and populating LucidDB.
# Run as root.

source env.sh
pwd=`pwd`

cd $ROOT_DIR
url=http://build.dynamobi.com/job/dy_dev_initbuild/70/label=lin64/artifact/luciddb/dist/luciddb.tar.bz2
wget $url
tar jxf luciddb.tar.bz2
rm -f luciddb.tar.bz2
mv luciddb-0.0.0 luciddb
cd luciddb/install
./install.sh
cd $ROOT_DIR
cat $pwd/load_data.sql | ./luciddb/bin/sqllineEngine
rm -rf /luciddb
cp -r luciddb /
rm -rf luciddb
chmod -R +r /luciddb
