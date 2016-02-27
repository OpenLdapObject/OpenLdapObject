<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Collection\EntityCollection;
use OpenLdapObject\Entity;
use OpenLdapObject\Annotations as OLO;

/**
 * @OLO\Dn(value="ou=organisation")
 * @OLO\Entity({"groupOfNames", "top"})
 */
class OrganisationSingle extends Entity
{
    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $cn;

    /**
     * @var EntityCollection
     * @OLO\Column(type="entity")
     * @OLO\EntityRelation(classname="OpenLdapObject\Tests\Manager\People", multi=false)
     */
    private $member;

    public function getCn()
    {
        return $this->cn;
    }

    public function setCn($value)
    {
        $this->cn = $value;
        return $this;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setMember($value)
    {
        $this->member = $value;
        return $this;
    }

}