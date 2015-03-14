<?php

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
        try {
            EntityManager::addEntityManager('default', $client);
        } catch(\InvalidArgumentException $e) {

        }
        $this->em = EntityManager::getEntityManager();
    }

    public function testQuery() {
        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis');

        $this->assertEquals($people->getUid(), 'pdeparis');
        $this->assertEquals($people->getSn(), 'Deparis');
        $this->assertEquals($people->getCn(), 'Pierre Deparis');
        $this->assertEquals($people->getGivenName(), 'Pierre');
        $this->assertEquals($people->getMail(), 'pierre.deparis@example.com');
        $this->assertEquals($people->getTelephoneNumber(), array('03 00 00 00 01', '04 00 00 00 01'));
    }

    public function testFlush() {
        $newPeople = new People();
        $newPeople
            ->setUid('mdubois')
            ->setCn('Maurice Dubois')
            ->setSn('Dubois')
            ->setGivenName('Maurice')
            ->setMail('maurice.dubois@example.com')
            ->addTelephoneNumber('03 00 00 00 02');

        $this->em->persist($newPeople);
        $this->em->flush();

        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('mdubois');
        $people->setUid('maurice.dubois');

        $this->em->persist($people);
        $this->em->flush();

        $people = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('maurice.dubois');
        $this->em->remove($people);
        $this->em->flush();
    }
}
 