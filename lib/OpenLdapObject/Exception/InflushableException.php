<?php

namespace OpenLdapObject\Exception;


class InflushableException extends \Exception {
    public function __construct($msg) {
        parent::__construct($msg);
    }
} 