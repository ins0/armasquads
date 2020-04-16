#!/bin/bash

maxcounter=45
counter=1
while ! mysql --protocol TCP -u"$MYSQL_USER" -p"$MYSQL_PWD" -h ${MYSQL_HOST} -e "show databases;" > /dev/null 2>&1; do
    sleep 1
    counter=`expr $counter + 1`
    if [ $counter -gt $maxcounter ]; then
        >&2 echo "We have been waiting for MySQL too long already; failing."
        exit 1
    fi;
done

tables=$(echo "SHOW TABLES;" | mysql ${MYSQL_DB} -s -u ${MYSQL_USER} -p${MYSQL_PWD} -h ${MYSQL_HOST})
tableCount=$(echo "$tables" | wc -w)

if [ "$tableCount" -eq "0" ]; then
    mysql ${MYSQL_DB} -s -u ${MYSQL_USER} -p${MYSQL_PWD} -h ${MYSQL_HOST} < /app/init_database.sql
    echo "INIT ARMASQUADS DATABASE"
fi