<?php

namespace OpenLdapObject\Annotations;

/**
 * Class EntityRelation
 * @package OpenLdapObject\Annotations
 * @Annotation
 */
class EntityRelation implements Annotation
{
    public $classname;
    public $multi;
    public $ignore_errors = false;

    public function check()
    {
        return (is_bool($this->multi) && !empty($this->classname) && is_bool($this->ignore_errors));
    }


} 