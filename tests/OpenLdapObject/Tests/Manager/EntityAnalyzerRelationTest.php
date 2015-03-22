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
        $this->entityAnalyzer = EntityAnalyzer::get('OpenLdapObject\Tests\Manager\OrganisationTest');
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

    public function testGetClassAnnotation() {
        $this->assertEquals($this->entityAnalyzer->getClassAnnotation(), array(
                'dn' => 'ou=organisation',
                'objectclass' => array('groupOfNames', 'top')
            )
        );
    }

    public function testGetBaseDn() {
        $this->assertEquals($this->entityAnalyzer->getBaseDn(), 'ou=organisation');
    }

    public function testListRequiredMethod() {
        $this->assertEquals($this->entityAnalyzer->listRequiredMethod(), array(
            'getCn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'cn'),
            'setCn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'cn'),
            'getMember' => array('type' => EntityAnalyzer::GETTER, 'column' => 'member'),
            'addMember' => array('type' => EntityAnalyzer::ADDER, 'column' => 'member'),
            'removeMember' => array('type' => EntityAnalyzer::REMOVER, 'column' => 'member')
        ));
    }

    public function testListMissingMethod() {
        $this->assertEquals($this->entityAnalyzer->listMissingMethod(), array(
            'getCn' => array('type' => EntityAnalyzer::GETTER, 'column' => 'cn'),
            'setCn' => array('type' => EntityAnalyzer::SETTER, 'column' => 'cn'),
            'getMember' => array('type' => EntityAnalyzer::GETTER, 'column' => 'member'),
            'addMember' => array('type' => EntityAnalyzer::ADDER, 'column' => 'member'),
            'removeMember' => array('type' => EntityAnalyzer::REMOVER, 'column' => 'member')
        ));
    }

    public function testIsEntityRelation() {
        $this->assertEquals($this->entityAnalyzer->isEntityRelation('member'), true);
        $this->assertEquals($this->entityAnalyzer->isEntityRelation('cn'), false);
    }

    public function testIsMultiEntityRelation() {
        $this->assertEquals($this->entityAnalyzer->isEntityRelationMultiple('member'), true);
        $this->assertEquals($this->entityAnalyzer->isEntityRelationMultiple('cn'), false);
    }

}