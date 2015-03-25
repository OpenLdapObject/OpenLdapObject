<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Collection\EntityCollection;
use OpenLdapObject\Entity;
use OpenLdapObject\Annotations as OLO;

/**
 * @OLO\Dn(value="ou=organisation")
 * @OLO\Entity({"groupOfNames", "top"})
 */
class Organisation extends Entity {
    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $cn;

    /**
     * @var EntityCollection
     * @OLO\Column(type="entity")
     * @OLO\EntityRelation(classname="OpenLdapObject\Tests\Manager\People", multi=true)
     */
    private $member;

    public function getCn() {
        return $this->cn;
    }

    public function setCn($value) {
        $this->cn = $value;
        return $this;
    }

    public function getMember() {
        return $this->member;
    }

    public function addMember($value) {
        $this->member->append($value);
        return $this;
    }

    public function removeMember($value) {
        $this->member->removeElement($value);
        return $this;
    }

}