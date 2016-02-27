<?php

namespace OpenLdapObject\Tests\Builder;

use OpenLdapObject\Builder\Method\AdderBuilder;
use OpenLdapObject\Builder\Method\GetterBuilder;
use OpenLdapObject\Builder\Method\SetterBuilder;
use OpenLdapObject\Builder\Method\RemoverBuilder;

class MethodBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetter()
    {
        $this->assertEquals((new GetterBuilder('uid'))->getMethodSrc(),
            '    public function getUid()
    {
        return $this->uid;
    }

');
    }

    public function testSetter()
    {
        $this->assertEquals((new SetterBuilder('mail'))->getMethodSrc(),
            '    public function setMail($value)
    {
        $this->mail = $value;
        return $this;
    }

');
    }

    public function testAdder()
    {
        $this->assertEquals((new AdderBuilder('telephoneNumber'))->getMethodSrc(),
            '    public function addTelephoneNumber($value)
    {
        $this->telephoneNumber->add($value);
        return $this;
    }

');
    }

    public function testRemover()
    {
        $this->assertEquals((new RemoverBuilder('telephoneNumber'))->getMethodSrc(),
            '    public function removeTelephoneNumber($value)
    {
        $this->telephoneNumber->removeElement($value);
        return $this;
    }

');
    }
}