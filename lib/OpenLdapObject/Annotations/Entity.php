<?php

namespace OpenLdapObject\Annotations;

/**
 * Class Dn
 * @package OpenLdapObject\Annotations
 * @Annotation
 */
class Entity implements Annotation {
    public $objectclass;

    public function check() {
        if(is_null($this->objectclass)) {
            throw new InvalidAnnotationException($this, 'objectclass', 'objectclass mustn\'t be empty');
        }
    }
}