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
        $this->assertEquals($people->getTelephoneNumber()->toArray(), array('03 00 00 00 01', '04 00 00 00 01'));
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

    public function testRelation() {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('state');

        $uid = array();
        foreach($org->getMember() as $value) {
            $uid[] = $value->getUid();
        }
        $this->assertEquals($uid, array('pdeparis', 'mdupont'));
    }

    public function testRelationNull() {
        $org = $this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('bad');
        $this->assertEquals($org->getMember()[1], false);

        $org->removeMember(false);

        $this->em->persist($org);
        $this->em->flush();

        $this->em->getClient()->update($org->_getDn(), array('member' => array($org->getMember()[0]->_getDn(), 'uid=youdi,ou=people,dc=example,dc=com')));
    }

	public function testNewWithEntityCollection() {
		$org = new Organisation();
		$org->setCn('test');
		$org->addMember($this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('pdeparis'));

		$this->em->persist($org);
		$this->em->flush();

		$this->em->remove($org);
		$this->em->flush();
	}

	/**
	 * @expectedException OpenLdapObject\Exception\InvalidHydrateException
	 */
	public function testWithStrict() {
		$user = $this->em->getRepository('\OpenLdapObject\Tests\Manager\People')->find('jdoe');
	}

	public function testWithNoStrict() {
		$user = $this->em->getRepository('\OpenLdapObject\Tests\Manager\PeopleNonStrict')->find('jdoe');

		$this->assertEquals($user->getGivenName(), 'John');
	}
}
 
