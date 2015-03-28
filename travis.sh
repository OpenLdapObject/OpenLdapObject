#!/bin/bash

/opt/apacheds-2.0.0-M19/bin/apacheds start default

sleep 10

ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret
ldapadd -c -H ldap://localhost:10389 -x -D "uid=admin,ou=system" -f tests/docker-test/data.ldif -w secret

composer install;
mv tests/OpenLdapObject/Tests/TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php.origin;
cp tests/docker-test/TestConfiguration.php tests/OpenLdapObject/Tests/TestConfiguration.php

phpunit
