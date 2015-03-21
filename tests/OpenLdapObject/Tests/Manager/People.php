<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Annotations as OLO;
use OpenLdapObject\Entity;

/**
 * @OLO\Dn(value="ou=people")
 * @OLO\Entity({"inetOrgPerson", "organizationalPerson", "person", "top"})
 */
class People extends Entity {
    /**
     * @OLO\Column(type="string")
     * @OLO\Index
     */
    private $uid;

    /**
     * @OLO\Column(type="string")
     */
    private $cn;

    /**
     * @OLO\Column(type="string")
     */
    private $sn;

    /**
     * @OLO\Column(type="string")
     */
    private $givenName;

    /**
     * @OLO\Column(type="string")
     */
    private $mail;

    /**
     * @OLO\Column(type="array")
     */
    private $telephoneNumber;

    public function getUid() {
        return $this->uid;
    }

    public function setUid($value) {
        $this->uid = $value;
        return $this;
    }

    public function getCn() {
        return $this->cn;
    }

    public function setCn($value) {
        $this->cn = $value;
        return $this;
    }

    public function getSn() {
        return $this->sn;
    }

    public function setSn($value) {
        $this->sn = $value;
        return $this;
    }

    public function getGivenName() {
        return $this->givenName;
    }

    public function setGivenName($value) {
        $this->givenName = $value;
        return $this;
    }

    public function getMail() {
        return $this->mail;
    }

    public function setMail($value) {
        $this->mail = $value;
        return $this;
    }

    public function getTelephoneNumber() {
        return $this->telephoneNumber;
    }

    public function addTelephoneNumber($value) {
        $this->telephoneNumber[] = $value;
        return $this;
    }

    public function removeTelephoneNumber($value) {
        if(($key = array_search($value, $this->telephoneNumber)) !== false) {
            unset($this->telephoneNumber[$key]);
        }
        return $this;
    }

    public function addUid($value) {
        $this->uid[] = $value;
        return $this;
    }

    public function removeUid($value) {
        if(($key = array_search($value, $this->uid)) !== false) {
            unset($this->uid[$key]);
        }
        return $this;
    }

    public function addCn($value) {
        $this->cn[] = $value;
        return $this;
    }

    public function removeCn($value) {
        if(($key = array_search($value, $this->cn)) !== false) {
            unset($this->cn[$key]);
        }
        return $this;
    }

    public function addSn($value) {
        $this->sn[] = $value;
        return $this;
    }

    public function removeSn($value) {
        if(($key = array_search($value, $this->sn)) !== false) {
            unset($this->sn[$key]);
        }
        return $this;
    }

    public function addGivenName($value) {
        $this->givenName[] = $value;
        return $this;
    }

    public function removeGivenName($value) {
        if(($key = array_search($value, $this->givenName)) !== false) {
            unset($this->givenName[$key]);
        }
        return $this;
    }

    public function addMail($value) {
        $this->mail[] = $value;
        return $this;
    }

    public function removeMail($value) {
        if(($key = array_search($value, $this->mail)) !== false) {
            unset($this->mail[$key]);
        }
        return $this;
    }

}