<?php
namespace OpenLdapObject\Exception;


use OpenLdapObject\LdapClient\Connection;

class BadIdentificationException extends \Exception {
    public function __construct(Connection $connection) {
        parent::__construct('Unable to identify to ' . $connection->getHostname() . ':' . $connection->getPort() . ' with username ' . $connection->getUsername() . ' and password');
    }
} 