<?php

namespace OpenLdapObject\Tests\Manager;

use OpenLdapObject\Annotations as OLO;
use OpenLdapObject\Entity;

/**
 * @OLO\Dn(value="ou=people")
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

}