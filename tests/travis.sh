#!/bin/bash

sudo /opt/apacheds-2.0.0-M20/bin/apacheds start default

STATUS=0
while [ "$STATUS" != "1" ];
do
    sleep 1;
    STATUS=$(sudo netstat -lapute | grep "10389" | wc -l);
done

sudo ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret
sudo ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret
sudo ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret

composer install;
mv tests/OpenLdapObject/Tests/TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php.origin;
cp tests/docker-test/TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php

phpunit
