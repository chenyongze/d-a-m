#!/bin/sh
update="http://manager.mofang.com/api/update/"
chown -R www:www /data/www/db.mofang.com
chmod -R 755 /data/www/db.mofang.com
rsync -avzP --password-file=/etc/rsyncd.pwd /data/www/db.mofang.com/* mofang@42.62.77.208::salt-rsync/db.mofang.com

curl -d "{'username':'huxiaojun','password':'hxj000000','groups':'api','project':'db.mofang.com'}" $update
