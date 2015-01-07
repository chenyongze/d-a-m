#!/bin/sh
update="http://manager.mofang.com/api/update/"
chown -R www:www /data/www/db.admin.mofang.com
chmod -R 755 /data/www/db.admin.mofang.com
rsync -avzP --password-file=/etc/rsyncd.pwd /data/www/db.admin.mofang.com/* mofang@42.62.77.208::salt-rsync/db.admin.mofang.com

curl -d "username=huxiaojun&password=hxj000000&groups=api&project=db.admin.mofang.com" $update 
