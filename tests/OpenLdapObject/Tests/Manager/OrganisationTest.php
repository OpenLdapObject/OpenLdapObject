<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Entity;
use OpenLdapObject\Annotations as OLO;

/**
 * @OLO\Dn(value="ou=organisation")
 * @OLO\Entity({"groupOfNames", "top"})
 */
class OrganisationTest extends Entity {
    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $cn;

    /**
     * @OLO\Column(type="entity")
     * @OLO\EntityRelation(classname="OpenLdapObject\Tests\Manager\People", multi=true)
     */
    private $member;
}