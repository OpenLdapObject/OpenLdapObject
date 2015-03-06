<?php
/**
 * Created by PhpStorm.
 * User: pierre
 * Date: 06/03/15
 * Time: 17:51
 */

namespace OpenLdapObject\Tests\Manager;


use OpenLdapObject\LdapClient\Connection;
use OpenLdapObject\Manager\EntityManager;

class RepositoryTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var EntityManager
     */
    private $em;

    public function setUp() {
        $connect = new Connection(LDAP_HOST, LDAP_PORT);
        $connect->identify(LDAP_USER, LDAP_PASSWORD);
        $client = $connect->connect();
        $client->setBaseDn(LDAP_BASE_DN);
        EntityManager::addEntityManager('default', $client);
        $this->em = EntityManager::getEntityManager();
    }

    public function testQuery() {
        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis');

        $this->assertEquals(count($people), 1);

        $people = $people[0];

        $this->assertEquals($people->getUid(), 'pdeparis');
        $this->assertEquals($people->getSn(), 'Deparis');
        $this->assertEquals($people->getCn(), 'Pierre Deparis');
        $this->assertEquals($people->getGivenName(), 'Pierre');
        $this->assertEquals($people->getMail(), 'pierre.deparis@example.com');
        $this->assertEquals($people->getTelephoneNumber(), array('03 00 00 00 01', '04 00 00 00 01'));
    }
}
 