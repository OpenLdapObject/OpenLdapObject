<?php
namespace OpenLdapObject\Exception;

use OpenLdapObject\LdapClient\Connection;

class ConnectionException extends \Exception {
    public function __construct(Connection $connection) {
        parent::__construct('Unable to connect to ' . $connection->getHostname() . ':' . $connection->getPort());
    }
}