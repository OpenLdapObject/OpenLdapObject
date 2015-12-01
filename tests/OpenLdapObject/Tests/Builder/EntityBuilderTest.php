<?php

namespace OpenLdapObject\Tests\Builder;


use OpenLdapObject\Builder\EntityBuilder;

class EntityBuilderTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \OpenLdapObject\Builder\EntityBuilder
     */
    private $entityBuilder;
    private $filePath;
    private $fileContent;

    public function setUp() {
        $this->entityBuilder = new EntityBuilder('OpenLdapObject\Tests\Manager\UncompletPeople');
        $this->filePath = __DIR__ . '/../Manager/UncompletPeople.php';
        $this->fileContent = file_get_contents($this->filePath);
    }

    public function tearDown() {
        file_put_contents($this->filePath, $this->fileContent);
    }

    public function testBuilder() {
        $this->assertEquals('74f91a238ed7123b7c78a308ba3dc81b', md5_file($this->filePath));
        $this->entityBuilder->completeEntity();
        $this->assertEquals('a8ebd1d8f7ecad19b9b8e3c2783f80db', md5_file($this->filePath));
    }
}
 