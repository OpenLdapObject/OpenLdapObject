<?php

namespace OpenLdapObject\Tests\LdapClient;


use OpenLdapObject\LdapClient\Connection;

class ConnectionTest extends \PHPUnit_Framework_TestCase {
    public function testGoodConnection() {
        $connection = new Connection(LDAP_HOST, LDAP_PORT);
        $connection->identify(LDAP_USER, LDAP_PASSWORD);
        $connection->connect();
    }

    /**
     * @expectedException OpenLdapObject\Exception\BadIdentificationException
     */
    public function testBadConnection() {
        $connection = new Connection(LDAP_BAD_HOST, LDAP_BAD_PORT);
        $connection->identify(LDAP_USER, LDAP_PASSWORD);
        $connection->connect();

    }

    /**
     * @expectedException OpenLdapObject\Exception\BadIdentificationException
     */
    public function testBadIdentify() {
        $connection = new Connection(LDAP_HOST, LDAP_PORT);
        $connection->identify(LDAP_BAD_USER, LDAP_BAD_PASSWORD);
        $connection->connect();
    }
}
 