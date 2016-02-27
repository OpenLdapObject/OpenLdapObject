<?php

namespace OpenLdapObject\Tests\LdapClient;


use OpenLdapObject\LdapClient\Client;
use OpenLdapObject\LdapClient\Connection;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setUp()
    {
        $connection = new Connection(LDAP_HOST, LDAP_PORT);
        $connection->identify(LDAP_USER, LDAP_PASSWORD);

        $this->client = $connection->connect();
    }

    public function testQuery()
    {
        $this->assertEquals($this->client->search('(&(objectclass=*)(telephoneNumber=03 00 00 00 01))', array('uid')), array(
            'count' => 1,
            0 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'pdeparis'
                ),
                0 => 'uid',
                'count' => 1,
                'dn' => 'uid=pdeparis,ou=people,dc=example,dc=com'
            )
        ));
    }

    public function testCleanResult()
    {
        $this->assertEquals($this->client->cleanResult(array(
            'count' => 2,
            0 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'pdeparis'
                ),
                0 => 'uid',
                'count' => 1,
                'dn' => 'uid=pdeparis,ou=people,dc=example,dc=com'
            ),
            1 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'mdupont'
                ),
                0 => 'uid',
                'count' => 1,
                'dn' => 'uid=mdupont,ou=people,dc=example,dc=com'
            )
        )), array(
            0 => array(
                'data' => array(
                    'uid' => 'pdeparis'
                ),
                'dn' => 'uid=pdeparis,ou=people,dc=example,dc=com'
            ),
            1 => array(
                'data' => array(
                    'uid' => 'mdupont'
                ),
                'dn' => 'uid=mdupont,ou=people,dc=example,dc=com'
            )
        ));
    }

    public function testCleanResultMultiLine()
    {
        $this->assertEquals($this->client->cleanResult(array(
            'count' => 2,
            0 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'pdeparis'
                ),
                'telephoneNumber' => array(
                    'count' => 2,
                    0 => '03 00 00 00 00',
                    1 => '04 00 00 00 00'
                ),
                0 => 'uid',
                1 => 'telephoneNumber',
                'count' => 2,
                'dn' => 'uid=pdeparis,ou=people,dc=example,dc=com'
            ),
            1 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'mdupont'
                ),
                'telephoneNumber' => array(
                    'count' => 1,
                    0 => '03 00 00 00 01'
                ),
                0 => 'uid',
                1 => 'telephoneNumber',
                'count' => 2,
                'dn' => 'uid=mdupont,ou=people,dc=example,dc=com'
            )
        )), array(
            0 => array(
                'data' => array(
                    'uid' => 'pdeparis',
                    'telephoneNumber' => array('03 00 00 00 00', '04 00 00 00 00')
                ),
                'dn' => 'uid=pdeparis,ou=people,dc=example,dc=com'
            ),
            1 => array(
                'data' => array(
                    'uid' => 'mdupont',
                    'telephoneNumber' => '03 00 00 00 01'
                ),
                'dn' => 'uid=mdupont,ou=people,dc=example,dc=com'
            )
        ));
    }

    public function testRead()
    {
        $this->assertEquals($this->client->read('uid=mdupont,ou=people,dc=example,dc=com', array('uid')), array(
            'count' => 1,
            0 => array(
                'uid' => array(
                    'count' => 1,
                    0 => 'mdupont'
                ),
                0 => 'uid',
                'count' => 1,
                'dn' => 'uid=mdupont,ou=people,dc=example,dc=com'
            )
        ));
    }

    public function testComplexeQuery()
    {
        $search = $this->client->search('(&(objectclass=*)(|(telephoneNumber=03 00 00 00 01)(telephoneNumber=03 00 00 00 00)))', array('uid'));
        $this->assertEquals($search['count'], 2);
    }
}
 
