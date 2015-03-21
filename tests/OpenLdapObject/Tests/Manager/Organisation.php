<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Entity;
use OpenLdapObject\Annotations as OLO;

class Organisation extends Entity {
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