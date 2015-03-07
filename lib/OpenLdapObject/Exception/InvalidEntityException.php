<?php

namespace OpenLdapObject\Exception;


class InvalidEntityException extends \Exception {
    public function __construct($msg) {
        parent::__construct($msg);
    }
} 