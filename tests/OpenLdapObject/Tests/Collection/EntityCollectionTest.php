<?php

namespace OpenLdapObject\Tests\Collection;


use OpenLdapObject\Collection\EntityCollection;
use OpenLdapObject\LdapClient\Connection;
use OpenLdapObject\Manager\EntityManager;
use OpenLdapObject\Tests\Manager\Organisation;

class EntityCollectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @var EntityCollection
	 */
	private $entityCollection;

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

		$this->entityCollection = new EntityCollection(EntityCollection::DN, $this->em->getRepository('\OpenLdapObject\Tests\Manager\People'), array('uid=mdupont,ou=people,dc=example,dc=com', 'uid=pdeparis,ou=people,dc=example,dc=com'));
	}

	public function testCount() {
		$this->assertEquals($this->entityCollection->count(), 2);
	}

	/**
	 * @expectedException OpenLdapObject\Exception\InvalidEntityException
	 */
	public function testAddStdType() {
		$org = new Organisation();
		$org->setCn('test');
		$org->addMember(true);
	}

	/**
	 * @expectedException OpenLdapObject\Exception\InvalidEntityException
	 */
	public function testAddBadEntity() {
		$org = new Organisation();
		$org->setCn('test');
		$org->addMember($this->em->getRepository('\OpenLdapObject\Tests\Manager\Organisation')->find('state'));
	}
}