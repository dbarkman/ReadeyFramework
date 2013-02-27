#!/bin/sh
#
# Log Rotate Script for ReadeyFramework Logs
# Creating Date: 02/26/2013
# Author: David Barkman david.barkman13@gmail.com
# Last Updated: 02/26/2013
# Last Updated By: David Barkman david.barkman13@gmail.com
#
# Saves logs for ten (10) days
#
# Variables
timestamp=`date +%Y%m%d%H%M%S`
#
# Rename the current ReadeyFramework logs and zip it up tight
mv /srv/http/ReadeyFramework/logs/ReadeyFramework.log /srv/http/ReadeyFramework/logs/ReadeyFramework.log.$timestamp
touch /srv/http/ReadeyFramework/logs/ReadeyFramework.log
chown http:http /srv/http/ReadeyFramework/logs/ReadeyFramework.log*
chmod 664 /srv/http/ReadeyFramework/logs/ReadeyFramework.log
bzip2 /srv/http/ReadeyFramework/logs/ReadeyFramework.log.$timestamp

#
# Cleanup all but the last 10 log files
#
count=`ls -d /srv/http/ReadeyFramework/logs/ReadeyFramework.log* | wc -l`
while (( $count > 10 ))
do
        oldLog=`ls -dtr /srv/http/ReadeyFramework/logs/ReadeyFramework.log* | head -1`
        rm -rf $oldLog
        count=`ls -d /srv/http/ReadeyFramework/logs/ReadeyFramework.log* | wc -l`
done
#
