<?php
namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Annotations\InvalidAnnotationException;
use OpenLdapObject\Manager\EntityAnalyzer;

class EntityAnalyzerRelationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \OpenLdapObject\Manager\EntityAnalyzer
     */
    private $entityAnalyzer;

    public function setUp() {
        $this->entityAnalyzer = EntityAnalyzer::get('OpenLdapObject\Tests\Manager\Organisation');
    }

    public function testListField() {
        $this->assertEquals($this->entityAnalyzer->listColumns(), array(
            'cn' => array('type' => 'string', 'index' => true),
            'member' => array('type' => 'entity', 'index' => false, 'relation' => array('classname' => 'OpenLdapObject\Tests\Manager\People', 'multi' => true))
            )
        );
    }

    /**
     * @expectedException OpenLdapObject\Annotations\InvalidAnnotationException
     */
    public function testListFieldMultiIndex() {
        $entityAnalyzer = EntityAnalyzer::get('OpenLdapObject\Tests\Manager\OrganisationInvalid');
        $entityAnalyzer->listColumns();
    }
    /*
    public function testGetClassAnnotation() {
        $this->assertEquals($this->entityAnalyzer->getClassAnnotation(), array(
                'dn' => 'ou=people',
                'objectclass' => array('inetOrgPerson', 'organizationalPerson', 'person', 'top')
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
    */
}