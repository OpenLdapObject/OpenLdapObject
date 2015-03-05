<?php

namespace OpenLdapObject\Annotations;

/**
 * Class Dn
 * @package OpenLdapObject\Annotations
 * @Annotation
 */
class Dn implements Annotation {
    public $value;

    public function check() {
        if(substr($this->value, 0, 1) == ',' || substr($this->value, -1) == ',') {
            throw new InvalidAnnotationException($this, 'value', 'Dn value mustn\'t start or end with a comma');
        }
    }
}