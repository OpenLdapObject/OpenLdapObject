<?php

namespace OpenLdapObject\Tests\Builder;


use OpenLdapObject\Builder\EntityBuilder;

class EntityBuilderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \OpenLdapObject\Builder\EntityBuilder
     */
    private $entityBuilder;

    public function setUp() {
        $this->entityBuilder = new EntityBuilder('OpenLdapObject\Tests\Manager\PeopleTest');
    }

    public function testGetter() {
        $this->assertEquals($this->entityBuilder->createGetter('uid'),
'    public function getUid() {
        return $this->uid;
    }

');
    }

    public function testSetter() {
        $this->assertEquals($this->entityBuilder->createSetter('mail'),
'    public function setMail($value) {
        $this->mail = $value;
        return $this;
    }

');
    }

    public function testAdder() {
        $this->assertEquals($this->entityBuilder->createAdder('telephoneNumber'),
'    public function addTelephoneNumber($value) {
        $this->telephoneNumber[] = $value;
        return $this;
    }

');
    }

    public function testRemover() {
        $this->assertEquals($this->entityBuilder->createRemover('telephoneNumber'),
'    public function removeTelephoneNumber($value) {
        if(($key = array_search($value, $this->telephoneNumber)) !== false) {
            unset($this->telephoneNumber[$key]);
        }
        return $this;
    }

');
    }

    public function testBuilder() {
        $entityBuilder = new EntityBuilder('OpenLdapObject\Tests\Manager\People');
        $entityBuilder->completeEntity();
    }
}
 