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
        $this->entityAnalyzer = new EntityAnalyzer('OpenLdapObject\Tests\Manager\PeopleTest');
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

    public function testGetBaseDn() {
        $this->assertEquals($this->entityAnalyzer->getBaseDn(), 'ou=people');
    }

    public function testListRequiredMethod() {
        $this->assertEquals($this->entityAnalyzer->listRequiredMethod(), array(
            'getUid' => array('type' => EntityAnalyzer::GETTER, 'column' => 'uid'),
            'setUid' => array('type' => EntityAnalyzer::SETTER, 'column' => 'uid'),
            'getCn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'cn'),
            'setCn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'cn'),
            'getSn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'sn'),
            'setSn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'sn'),
            'getGivenName' => array('type' => EntityAnalyzer::GETTER, 'column' => 'givenName'),
            'setGivenName' => array('type' => EntityAnalyzer::SETTER, 'column' => 'givenName'),
            'getMail' => array('type' => EntityAnalyzer::GETTER, 'column' => 'mail'),
            'setMail' => array('type' => EntityAnalyzer::SETTER, 'column' => 'mail'),
            'getTelephoneNumber' => array('type' => EntityAnalyzer::GETTER, 'column' => 'telephoneNumber'),
            'addTelephoneNumber' => array('type' => EntityAnalyzer::ADDER, 'column' => 'telephoneNumber'),
            'removeTelephoneNumber' => array('type' => EntityAnalyzer::REMOVER, 'column' => 'telephoneNumber')
        ));
    }

    public function testListMissingMethod() {
        $this->assertEquals($this->entityAnalyzer->listMissingMethod(), array(
            'getUid' => array('type' => EntityAnalyzer::GETTER, 'column' => 'uid'),
            'setUid' => array('type' => EntityAnalyzer::SETTER, 'column' => 'uid'),
            'getCn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'cn'),
            'setCn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'cn'),
            'getSn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'sn'),
            'setSn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'sn'),
            'getGivenName' => array('type' => EntityAnalyzer::GETTER, 'column' => 'givenName'),
            'setGivenName' => array('type' => EntityAnalyzer::SETTER, 'column' => 'givenName'),
            'getMail' => array('type' => EntityAnalyzer::GETTER, 'column' => 'mail'),
            'setMail' => array('type' => EntityAnalyzer::SETTER, 'column' => 'mail'),
            'getTelephoneNumber' => array('type' => EntityAnalyzer::GETTER, 'column' => 'telephoneNumber'),
            'addTelephoneNumber' => array('type' => EntityAnalyzer::ADDER, 'column' => 'telephoneNumber'),
            'removeTelephoneNumber' => array('type' => EntityAnalyzer::REMOVER, 'column' => 'telephoneNumber')
        ));
    }
}