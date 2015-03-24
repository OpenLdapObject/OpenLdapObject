<?php

namespace OpenLdapObject\Tests\Hydrate;


use OpenLdapObject\Manager\Hydrate\Hydrater;

class HydraterTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Hydrater
     */
    private $hydrater;

    public function setUp() {
        $this->hydrater = new Hydrater('OpenLdapObject\Tests\Manager\People');
    }

    public function testHydrate() {
        $array = array(
            'uid' => 'pdeparis',
            'telephoneNumber' => array('03 00 00 00 00', '04 00 00 00 00'),
            'givenName' => 'Pierre'
        );
        $people = $this->hydrater->hydrate($array);
        $this->assertEquals($people->getUid(), 'pdeparis');
        $this->assertEquals($people->getTelephoneNumber(), array('03 00 00 00 00', '04 00 00 00 00'));
        $this->assertEquals($people->getGivenName(), 'Pierre');
    }

    public function testGetData() {
        $array = array(
            'uid' => 'pdeparis',
            'telephoneNumber' => array('03 00 00 00 00', '04 00 00 00 00')
        );
        $people = $this->hydrater->hydrate($array);

        $this->assertEquals($this->hydrater->getData($people), array(
            'uid' => 'pdeparis',
            'telephoneNumber' => array('03 00 00 00 00', '04 00 00 00 00'),
            'cn' => null,
            'sn' => null,
            'givenName' => null,
            'mail' => null
        ));
    }
}
 