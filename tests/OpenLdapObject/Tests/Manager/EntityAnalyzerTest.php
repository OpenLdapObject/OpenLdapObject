<?php
namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Annotations\InvalidAnnotationException;
use OpenLdapObject\Manager\EntityAnalyzer;

class EntityAnalyzerTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \OpenLdapObject\Manager\EntityAnalyzer
     */
    private $entityAnalyzer;

    public function setUp() {
        $this->entityAnalyzer = new EntityAnalyzer('OpenLdapObject\Tests\Manager\People');
    }

    public function testListField() {
        $this->assertEquals($this->entityAnalyzer->listColumns(), array(
            'uid' => array('type' => 'string', 'index' => true),
            'cn' => array('type' => 'string', 'index' => false),
            'sn' => array('type' => 'string', 'index' => false),
            'givenName' => array('type' => 'string', 'index' => false),
            'mail' => array('type' => 'string', 'index' => false),
            'telephoneNumber' => array('type' => 'array', 'index' => false)
            )
        );
    }

    /**
     * @expectedException OpenLdapObject\Annotations\InvalidAnnotationException
     */
    public function testListFieldMultiIndex() {
        $entityWithMultiIndex = new EntityAnalyzer('OpenLdapObject\Tests\Manager\PeopleMultiIndex');
        $this->assertEquals($entityWithMultiIndex->listColumns(), array(
                'uid' => array('type' => 'string', 'index' => true),
                'cn' => array('type' => 'string', 'index' => false),
                'sn' => array('type' => 'string', 'index' => false),
                'givenName' => array('type' => 'string', 'index' => false),
                'mail' => array('type' => 'string', 'index' => false),
                'telephoneNumber' => array('type' => 'array', 'index' => false)
            )
        );
    }

    public function testGetClassAnnotation() {
        $this->assertEquals($this->entityAnalyzer->getClassAnnotation(), array(
                'dn' => 'ou=people'
            )
        );
    }

    public function testListRequiredMethod() {
        $this->assertEquals($this->entityAnalyzer->listRequiredMethod(), array(
            'getUid',
            'setUid',
            'getCn',
            'setCn',
            'getSn',
            'setSn',
            'getGivenName',
            'setGivenName',
            'getMail',
            'setMail',
            'getTelephoneNumber',
            'addTelephoneNumber',
            'removeTelephoneNumber'
        ));
    }

    public function testListMissingMethod() {
        $this->assertEquals($this->entityAnalyzer->listMissingMethod(), array(
            'getUid',
            'setUid',
            'getCn',
            'setCn',
            'getSn',
            'setSn',
            'getGivenName',
            'setGivenName',
            'getMail',
            'setMail',
            'getTelephoneNumber',
            'addTelephoneNumber',
            'removeTelephoneNumber'
        ));
    }
}