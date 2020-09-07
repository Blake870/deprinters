echo "drop database hgrowndb"  | mysql -h 127.0.0.1 -uroot --password=""
echo "create database hgrowndb"  | mysql -h 127.0.0.1 -uroot --password=""
mysql -uroot --password="" hgrowndb < ../data/homegrown.sql
