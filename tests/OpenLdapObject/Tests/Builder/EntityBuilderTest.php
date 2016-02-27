<?php

namespace OpenLdapObject\Tests\Builder;


use OpenLdapObject\Builder\EntityBuilder;

class EntityBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \OpenLdapObject\Builder\EntityBuilder
     */
    private $entityBuilder;
    private $filePath;
    private $fileContent;

    public function setUp()
    {
        $this->entityBuilder = new EntityBuilder('OpenLdapObject\Tests\Manager\UncompletPeople');
        $this->filePath = __DIR__ . '/../Manager/UncompletPeople.php';
        $this->fileContent = file_get_contents($this->filePath);
    }

    public function tearDown()
    {
        file_put_contents($this->filePath, $this->fileContent);
    }

    public function testBuilder()
    {
        $this->assertEquals('548e5ced3733e98acc7672eb2862a8bf', md5_file($this->filePath));
        $this->entityBuilder->completeEntity();
        $this->assertEquals('a12729b72e1b330d52f832b54e771235', md5_file($this->filePath));
    }
}
 