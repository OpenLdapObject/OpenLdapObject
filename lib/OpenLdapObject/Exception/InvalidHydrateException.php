<?php

namespace OpenLdapObject\Exception;


class InvalidHydrateException extends \Exception {
    public function __construct($msg) {
        parent::__construct($msg);
    }
} 